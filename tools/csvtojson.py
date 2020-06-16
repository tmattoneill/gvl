import csv, json
from   datetime import datetime as dt 
import time
import os

file            = "../data/gid_domain_list.csv"
json_fh         = "../data/gid_domains.json"
json_data       = {}
csv_create_time = os.path.getmtime(file)
td_format       = "%Y-%m-%d %H:%M:%SZ"

json_data["dataSource"]     = file
json_data["jsonUpdateTime"] = dt.strftime(dt.now(),td_format)
json_data["csvCreateTime"]  = time.strftime(td_format, time.localtime(csv_create_time))
json_data["data"]           = {}

with open(file, encoding='utf-8-sig', newline='\n') as csv_file:
    csvReader = csv.DictReader(csv_file)

    for rows in csvReader:
        
        if rows['gid'].strip() is not None and rows['gid'] != '':
            gid = rows['gid']
            json_data['data'][gid] = { 
                        "company_name": rows['company_name'],
                        "domains": [rows['domain']]
            }
        else:
            json_data['data'][gid]["domains"].append(rows['domain'])


with open(json_fh, 'w') as json_file:
    json_file.write(json.dumps(json_data, indent=4))
