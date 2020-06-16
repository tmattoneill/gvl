import csv, json

file      = "gid_domain_list.csv"
json_fh   = "gid_domains.json"
json_data = {}

with open(file, encoding='utf-8-sig', newline='\n') as csv_file:
    csvReader = csv.DictReader(csv_file)

    for rows in csvReader:
        
        if rows['gid'].strip() is not None and rows['gid'] != '':
            gid = rows['gid']
            json_data[gid] = { 
                        "company_name": rows['company_name'],
                        "domains": [rows['domain']]
            }
        else:
            json_data[gid]["domains"].append(rows['domain'])


with open(json_fh, 'w') as json_file:
    json_file.write(json.dumps(json_data, indent=4))
