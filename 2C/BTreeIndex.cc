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
    return 0;
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
    return 0;
}


//Record the path from root to leaf when searching for a key
RC pathRecord(PageId rootPid, PageId path[], int& curLevel, int key) {
	if (rootPid < 1) {
		return RC_INVALID_PID;
	}
	return 0;

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
    return 0;
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
