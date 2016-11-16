/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = 1;
    treeHeight = 1;
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
	RC rc;
	if ((rc = pf.open(indexname, mode)) < 0) {
		return rc;
	}
	char tmpBuffer[PageFile::PAGE_SIZE];
	int offset;
	if (pf.endPid() == 0) {

		// index not initialized yet, need to initialize a leafnode
		BTLeafNode firstNode;
		memcpy(&tmpBuffer, &rootPid, sizeof(PageId));
		offset = sizeof(PageId);
		memcpy(&tmpBuffer + offset, &treeHeight, sizeof(int));
		pf.write(0, tmpBuffer);

		//write firstnode into pagefile;
		firstNode.write(1, pf);


	} else {
		//index is already initialized, read the rootPid and height of the tree
		
		if ((rc = pf.read(0, tmpBuffer)) < 0) {
			return rc;
		}
		memcpy(&rootPid, &tmpBuffer, sizeof(PageId));
		offset = sizeof(PageId);
		memcpy(&treeHeight, &tmpBuffer + offset, sizeof(int));
	}
    return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
	char tmpBuffer[PageFile::PAGE_SIZE];
	memcpy(&tmpBuffer, &rootPid, sizeof(PageId));
	int offset = sizeof(PageId);
	memcpy(&tmpBuffer + offset, &treeHeight, sizeof(int));
	RC rc;
	rc = pf.write(0, tmpBuffer);
	if (rc < 0) {
		return rc;
	}

    return pf.close();
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
    RC rc;
    PageId path[treeHeight];
    IndexCursor cursor;
    if ((rc = pathRecord(path, 1, key, cursor)) < 0) return rc;
    return recursiveInsert(0, path, key, rid, 0);
}
/*
 * Recursive function execute actual insertion process
 * @param curLevel[IN] current level start from 0
 * @param path[][IN] path recording insertion
 * @param key[OUT] the key insert, for leaf, non-leaf and new root
 * @param rid[OUT] the rid insert upwards, for leaf only
 * @param pid[OUT] the pid insert upwards, for new root only
 *
 */
RC BTreeIndex::recursiveInsert(int curLevel, PageId path[], int key, const RecordId& rid, const PageId& pid) {
    RC rc;
    // insert a new root, after this this function stops
    if(curLevel == treeHeight) {
        BTNonLeafNode newRoot;
        rootPid = pf.endPid();
        treeHeight++;
        newRoot.initializeRoot(path[curLevel - 1],key,pid);
        newRoot.write(rootPid, pf);
        char rootInfo[PageFile::PAGE_SIZE];
        memcpy(rootInfo, &rootPid, sizeof(PageId));
        memcpy(rootInfo, &treeHeight, sizeof(int));
        pf.write(0, rootInfo);
        return 0;
    }
    if(curLevel == 0) {
        BTLeafNode leaf;
        leaf.read(path[treeHeight - curLevel - 1],pf);// read data into root1's buffer
        //TODO: double check the capacity
        if(leaf.getKeyCount() < leaf.ENTRY_SIZE) {
            if ((rc = leaf.insert(key, rid)) < 0) return rc;
            if ((rc = leaf.write(path[treeHeight - curLevel - 1], pf)) < 0) return rc;
            return 0;
        }
        else{
            PageId newSiblingId = pf.endPid();
            int newKey;
            BTLeafNode newSibling;
            if ((rc = leaf.insertAndSplit(key, rid, newSibling, newKey)) < 0) return rc;
            if ((rc = leaf.setNextNodePtr(newSiblingId)) < 0) return rc;
            if ((rc = leaf.write(path[treeHeight - curLevel - 1], pf)) < 0) return rc;
            if ((rc = newSibling.write(newSiblingId, pf)) < 0) return rc;
            return recursiveInsert(curLevel + 1, path, newKey, rid, newSiblingId);
        }

    }
    else {
        BTNonLeafNode node;
        node.read(path[treeHeight - curLevel - 1],pf);
        if(node.getKeyCount() < node.KEY_CAPACITY) {
            if ((rc = node.insert(key, pid)) < 0) return rc;
            if ((rc = node.write(path[treeHeight - curLevel - 1], pf)) < 0) return rc;
            return 0;
        }
        else{
            PageId newSiblingId = pf.endPid();
            int newKey;
            BTNonLeafNode newSibling;
            if ((rc = node.insertAndSplit(key, pid, newSibling, newKey)) < 0) return rc;
            if ((rc = node.write(path[treeHeight - curLevel - 1], pf)) < 0) return rc;
            if ((rc = newSibling.write(newSiblingId, pf)) < 0) return rc;
            return recursiveInsert(curLevel + 1, path, newKey, rid, newSiblingId);
        }
    }
}

//Record the path from root to leaf when searching for a key
RC BTreeIndex::pathRecord(PageId path[], int curLevel, int key, IndexCursor& cursor) {

	RC rc;
	if (curLevel == treeHeight) {
		BTLeafNode leaf;
		rc = leaf.read(path[curLevel - 1], pf);
		if (rc < 0) {
			return RC_FILE_READ_FAILED;
		}
		int eid;
		rc = leaf.locate(path[curLevel - 1], eid);
		if (rc < 0) {
			return RC_NO_SUCH_RECORD; 
		}
		cursor.pid = path[curLevel - 1];
		cursor.eid= eid;
		return rc;

	} else {
		BTNonLeafNode nonLeaf;
		rc = nonLeaf.read(path[curLevel - 1], pf);
		if (rc < 0) {
			return RC_FILE_READ_FAILED;
		}
		PageId childPid;
		rc = nonLeaf.locateChildPtr(key, childPid);
		if (rc < 0) {
			return rc;
		}
		path[curLevel] = childPid;
		rc = pathRecord(path, curLevel + 1, key, cursor);
		return rc;
	}

	

}


/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */


RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
	RC rc;
	PageId path[treeHeight];
	path[0] = rootPid;
	int curLevel = 1;
	rc = pathRecord(path, curLevel, searchKey, cursor);

    return rc;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{	
	RC rc;
    BTLeafNode node;
    if (cursor.eid <= 0) {
        return RC_INVALID_CURSOR;
    }
    if ((rc = node.read(cursor.pid, pf)) < 0) {
        return rc;
    }
    if(cursor.eid > node.getKeyCount()) {
        return RC_INVALID_CURSOR;
    }
    if ((rc = node.readEntry(cursor.eid, key, rid)) < 0) {
        return RC_NO_SUCH_RECORD;
    }
    cursor.eid++;
    if (cursor.eid > node.getKeyCount()) {
        cursor.pid = node.getNextNodePtr();
        cursor.eid = 1;
    }
    return 0;
    
}
