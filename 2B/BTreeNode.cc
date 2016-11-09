#include "BTreeNode.h"

using namespace std;

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf) { 
	RC rc;
	if (pid < 0 || pid > pf.endPid()) {
		return RC_INVALID_PID;
	}
	if ((rc = pf.read(pid, buffer)) < 0) {
		return RC_FILE_READ_FAILED;
	}
	return 0; 
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf) { 
	RC rc;
	if (pid < 0) {
		return RC_INVALID_PID;
	}
	if ((rc = pf.write(pid, buffer)) < 0) {
		return RC_FILE_WRITE_FAILED;
	}
	return 0; 
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount() { 
	int count;
	memcpy(&count, buffer, sizeof(int));
	return count;
	
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid) { 
	int count = getKeyCount();
	if (count > MAX_ENTRY_NUM) {
		return RC_NODE_FULL;
	}
	int eid = -1;
	RC rc;
	rc = locate(key, eid);
	int offset = 0;
	if (eid < 0) {
		offset = sizeof(int) + count * ENTRY_SIZE;
		memcpy(buffer + offset, &rid, sizeof(RecordId));
		offset += sizeof(rid);
		memcpy(buffer + offset, &key, sizeof(int));
	} else {

		for (int i = count; i >= eid; i--) {
			offset = sizeof(int) + i * ENTRY_SIZE;
			memcpy(buffer + offset, buffer + offset - ENTRY_SIZE, ENTRY_SIZE);
		}
		offset = sizeof(int) + (eid - 1) * ENTRY_SIZE;
		memcpy(buffer + offset, &rid, sizeof(RecordId));
		offset += sizeof(rid);
		memcpy(buffer + offset, &key, sizeof(int));
	}
	count++;
	memcpy(buffer, &count, sizeof(int));
	
	return 0; 
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey) { 
	int count = getKeyCount();
	if (count < MAX_ENTRY_NUM) {
		return -1;
	}
	RC rc;
	int splitEid = count / 2 + 1;
	RecordId curRid;
	int curKey;

	int offset = 0;
	//copy the second half of current node to its sibling and clear  current node
	for (int i = splitEid; i <= count; i++) {
		offset = sizeof(int) + (i - 1) * ENTRY_SIZE;
		memcpy(&curRid, buffer + offset, sizeof(RecordId));
		
		memset(buffer + offset, 0, sizeof(RecordId));
		offset += sizeof(RecordId);
		memcpy(&curKey, buffer + offset, sizeof(int));
		if ((rc = sibling.insert(curKey, curRid)) < 0) {
			return rc;
		}
		memset(buffer + offset, 0, sizeof(int));
		if (i == splitEid) {
			siblingKey = curKey;
		}
	}
	// reset number of entries
	int newCount = splitEid - 1;
	memcpy(buffer, &newCount, sizeof(int));
	if (key > siblingKey) {
		rc = sibling.insert(key, rid);
		if (rc < 0) {
			return rc;
		}
	} else {
		rc = insert(key, rid);
		if (rc < 0) {
			return rc;
		}
	}


	return 0; 
}

/**
 * If searchKey exists in the node, set eid to the index entry
 * with searchKey and return 0. If not, set eid to the index entry
 * immediately after the largest index key that is smaller than searchKey,
 * and return the error code RC_NO_SUCH_RECORD.
 * Remember that keys inside a B+tree node are always kept sorted.
 * @param searchKey[IN] the key to search for.
 * @param eid[OUT] the index entry number with searchKey or immediately
                   behind the largest key smaller than searchKey.
 * @return 0 if searchKey is found. Otherwise return an error code.
 */
RC BTLeafNode::locate(int searchKey, int& eid) { 
	int count = getKeyCount();
	int tmpKey;
	int offset;
	for (int  i = 1; i <= count; i++) {
		offset = sizeof(int) + i * ENTRY_SIZE - sizeof(int);
		memcpy(&tmpKey, buffer + offset, sizeof(int));
		if (tmpKey == searchKey) {
			eid = i;
			return 0;
		} else if (tmpKey > searchKey) {
			eid = i;
			return RC_NO_SUCH_RECORD;
		}
	}
	return RC_NO_SUCH_RECORD; 
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid) { 
	RC rc;
	if (eid < 0 || eid > MAX_ENTRY_NUM) {
		return -1;
	}
	int offset = sizeof(int) + (eid - 1) * ENTRY_SIZE;
	memcpy(&rid, buffer + offset, sizeof(RecordId));
	offset += sizeof(RecordId);
	memcpy(&key, buffer + offset, sizeof(int));
	return 0; 
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr() { 
	PageId pid;
	int offset = PageFile::PAGE_SIZE - sizeof(PageId);
	memcpy(&pid, buffer + offset, sizeof(PageId));
	return 0; 
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid) { 
	int offset = PageFile::PAGE_SIZE - sizeof(PageId);
	memcpy(buffer + offset, &pid, sizeof(PageId));
	return 0; 
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{ return 0; }
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{ return 0; }

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{ return 0; }


/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{ return 0; }

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{ return 0; }

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{ return 0; }

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{ return 0; }
