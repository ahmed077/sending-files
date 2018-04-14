import sys, json
#print "Hello <br/>"

for x in sys.argv[1:]:
    if '-query' in x:
        query = x[7:]
        #print 'Query: ' + query
    if '-type' in x:
        type = x[6:]
        #print 'Type: ' + type
result = {}
result["response"]="You have searched for a "+type+" Named: "+query
print json.dumps(result)