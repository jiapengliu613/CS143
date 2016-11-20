/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"
#include <limits.h>
using namespace std;

// external functions and variables for load file and sql command parsing 
extern FILE* sqlin;
int sqlparse(void);


RC SqlEngine::run(FILE* commandline)
{
  fprintf(stdout, "Bruinbase> ");

  // set the command line input and start parsing user input
  sqlin = commandline;
  sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
               // SqlParser.y by bison (bison is GNU equivalent of yacc)

  return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
  RecordFile rf;   // RecordFile containing the table
  RecordId   rid;  // record cursor for table scanning

  RC     rc;
  int    key;     
  string value;
  int    count;
  int    diff;

  // open the table file
  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
    fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
    return rc;
  }

  BTreeIndex bTree;
  IndexCursor cursor;
  RC hasTree = bTree.open(table + ".idx", 'r');
  int minKey = INT_MIN;
  int maxKey = INT_MAX;
  int keyConCount = 0;
  int valConCount = 0;
  // convert the condition into a concised form
  for (unsigned i = 0; i < cond.size(); i++) {
    if (cond[i].attr == 2) {
      valConCount++;
      continue;
    }
    keyConCount++;
    int value = atoi(cond[i].value);
    switch (cond[i].comp) {
      case SelCond::EQ : 
        if (value < minKey || value > maxKey) {
          fprintf(stderr, "Error: SearchKey is out of range%s\n", table.c_str());
          goto exit_select;
        }
        minKey = value;
        maxKey = value;
        break;
      case SelCond::NE :
      // dont deal with the not equal condition until the condition check part
        break;
      case SelCond::LT :
        if (value <= minKey) {
          fprintf(stderr, "Error: SearchKey is out of range%s\n", table.c_str());
          goto exit_select;
        }
        maxKey = value - 1;
        break;
      case SelCond::GT :
        if (value >= maxKey) {
          fprintf(stderr, "Error: SearchKey is out of range%s\n", table.c_str());
          goto exit_select;
        }
        minKey = value + 1;
        break;
      case SelCond::LE :
        if (value < minKey) {
          fprintf(stderr, "Error: SearchKey is out of range%s\n", table.c_str());
          goto exit_select;
        }
        maxKey = value;
      case SelCond::GE :
        if (value > maxKey) {
          fprintf(stderr, "Error: SearchKey is out of range%s\n", table.c_str());
          goto exit_select;
        }
        minKey = value;
    } 
  }

  // scan the table file from the beginning
  rid.pid = rid.sid = 0;
  count = 0;
  // check if we need to use bTree to search the key
  if (keyConCount > 0 && hasTree == 0) {
    rc = bTree.locate(minKey, cursor);
    if (rc < 0) {
      fprintf(stderr, "No such Record%s\n", table.c_str());
      goto exit_select;
    }
    while (bTree.readForward(cursor, key, rid) == 0) {
      if (key > maxKey) {
        break;
      }
      // you need to read the value from the table if the following condition is satisfied
      if ((attr == 2 || attr == 3) || valConCount > 0) {
        rc = rf.read(rid, key, value);
      }
      if (rc < 0) {
        fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
        return rc;
      }
      bool isTrue = true;
      for (unsigned i = 0; i < cond.size(); i++) {
        switch (cond[i].attr) {
          case 1:
             diff = key - atoi(cond[i].value);
             break;
          case 2:
             diff = strcmp(value.c_str(), cond[i].value);
             break;
        }

        // skip the tuple if any condition is not met
        switch (cond[i].comp) {
          case SelCond::EQ:
            if (diff != 0) 
              isTrue = false;
            break;
          case SelCond::NE:
            if (diff == 0) 
              isTrue = false;
            break;
          case SelCond::GT:
            if (diff <= 0) 
              isTrue = false;
            break;
          case SelCond::LT:
            if (diff >= 0) 
              isTrue = false;
            break;
          case SelCond::GE:
            if (diff < 0) 
              isTrue = false;
            break;
          case SelCond::LE:
            if (diff > 0) 
              isTrue = false;
            break;
        }
        if (isTrue == false) {
          break;
        }
      }

      if (isTrue) {
        switch (attr) {
          case 1:  // SELECT key
            fprintf(stdout, "%d\n", key);
            break;
          case 2:  // SELECT value
            fprintf(stdout, "%s\n", value.c_str());
            break;
          case 3:  // SELECT *
            fprintf(stdout, "%d '%s'\n", key, value.c_str());
            break;
        }
        count++;
      }

    }



  } else {

    while (rid < rf.endRid()) {
      // read the tuple
      if ((rc = rf.read(rid, key, value)) < 0) {
        fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
        goto exit_select;
      }

      // check the conditions on the tuple
      for (unsigned i = 0; i < cond.size(); i++) {
        // compute the difference between the tuple value and the condition value
        switch (cond[i].attr) {
          case 1:
  	         diff = key - atoi(cond[i].value);
  	         break;
          case 2:
  	         diff = strcmp(value.c_str(), cond[i].value);
  	         break;
        }

        // skip the tuple if any condition is not met
        switch (cond[i].comp) {
          case SelCond::EQ:
    	       if (diff != 0) goto next_tuple;
          	 break;
          case SelCond::NE:
          	if (diff == 0) goto next_tuple;
          	break;
          case SelCond::GT:
          	if (diff <= 0) goto next_tuple;
          	break;
          case SelCond::LT:
          	if (diff >= 0) goto next_tuple;
          	break;
          case SelCond::GE:
          	if (diff < 0) goto next_tuple;
          	break;
          case SelCond::LE:
          	if (diff > 0) goto next_tuple;
          	break;
        }
      }

      // the condition is met for the tuple. 
      // increase matching tuple counter
      count++;

      // print the tuple 
      switch (attr) {
      case 1:  // SELECT key
        fprintf(stdout, "%d\n", key);
        break;
      case 2:  // SELECT value
        fprintf(stdout, "%s\n", value.c_str());
        break;
      case 3:  // SELECT *
        fprintf(stdout, "%d '%s'\n", key, value.c_str());
        break;
      }

      // move to the next tuple
      next_tuple:
      ++rid;
    }
  }

  // print matching tuple count if "select count(*)"
  if (attr == 4) {
    fprintf(stdout, "%d\n", count);
  }
  rc = 0;

  // close the table file and return
  exit_select:
  rf.close();
  return rc;
}

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{
  /* your code here */
  ifstream infile;
  infile.open(loadfile.c_str(), std::ifstream::in);
  if (!infile.is_open()) {
    fprintf(stderr, "Open file %s failed\n", loadfile.c_str());
    return RC_FILE_OPEN_FAILED;
  }

  RC rc;
  RecordFile rf;
  if (rc = rf.open(table + ".tbl", 'w') < 0) {
    fprintf(stderr, "Open table %s failed\n", table.c_str());
    return rc;
  }

  RecordId rid;
  string line;
  int key;
  string value;
  int cnt = 0;
  BTreeIndex bTreeIndex;
  if (index == true) {
      
      rc = bTreeIndex.open(table + ".idx", 'w');
      if (rc < 0) {
        fprintf(stderr, "Create B+Tree failed!\n");
        return rc;
      }
    
    while (!infile.eof() && infile.good()) {
      getline(infile, line);
      if (line == "") continue;
      if ((rc = parseLoadLine(line, key, value)) < 0) {
        fprintf(stderr, "Parsing line %d failed\n", cnt);
        goto exit_load;
      }
      if ((rc = rf.append(key, value, rid)) < 0) {
        fprintf(stderr, "Appending failed\n");
        goto exit_load;
      }
      if ((rc = bTreeIndex.insert(key, rid)) < 0) {
        fprintf(stderr, "Inserting into B+Tree failed!\n");
      }
      cnt++;
    }
  }
  fprintf(stdout, "Load succeeded! %d lines loaded\n", cnt);
  exit_load:
  infile.close();
  bTreeIndex.close();
  rf.close();
  return rc;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }

    // get the integer key value
    key = atoi(s);

    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }

    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }

    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }

    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }

    return 0;
}
