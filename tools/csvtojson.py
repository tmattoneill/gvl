import csv, json
from   datetime import datetime as dt 
import time
import os

# Initialise the script and set defaults
csv_file_location    = "../data/gid_domain_list.csv" # location of CSV
json_output_location = "../data/gid_domains.json"    # location for output
csv_create_time      = os.path.getmtime(csv_file_location)
td_format            = "%Y-%m-%d %H:%M:%SZ"
cnt_gid              = 0
cnt_dom              = 0
delim                = "\t"

# Set up the meta data and overall JSON file structure
json_data                   = {}
json_data["dataSource"]     = csv_file_location
json_data["jsonUpdateTime"] = dt.strftime(dt.now(),td_format)
json_data["csvCreateTime"]  = time.strftime(td_format, 
                                            time.localtime(csv_create_time))
json_data["data"]           = {}

with open(csv_file_location, encoding='utf-8-sig', newline='\n') as csv_file:
    csvReader = csv.DictReader(csv_file)

    for rows in csvReader:
        
        if rows['gid'].strip() is not None and rows['gid'] != '':
            cnt_gid += 1
            cnt_dom += 1
            gid = rows['gid']
            json_data['data'][gid] = { 
                        "company_name": rows['company_name'],
                        "domains": [rows['domain']]
            }
        else:
            cnt_dom += 1
            json_data['data'][gid]["domains"].append(rows['domain'])


with open(json_output_location, 'w') as json_file:
    json_file.write(json.dumps(json_data, indent=4))

print (f"Found {cnt_gid} unique GIDs and {cnt_dom} domains.\n")