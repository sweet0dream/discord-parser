import sys
import requests
from websocket import create_connection

arg = sys.argv[1:]
channel = sys.argv[1:][0]
message = sys.argv[1:][1]
token = 'MTA4MDE4MzgyNjU0MzgwNDQ2Ng.GbSxYc.yToWWQ0Lzv5ACXNcutZSCAyUXWGl3yRUKFXtkA'

s = requests.session()
s.headers.update({'authorization': token, 'Content-Type': 'application/json'})
payload = {"content":message, "tts":False}
ws = create_connection("wss://gateway.discord.gg/")
data = '''
{
    "op": 2,
    "d":{
        "token": "%s",
        "properties": {
            "$os": "linux",
            "$browser": "ubuntu",
            "$device": "ubuntu"
        },
    }
}
''' % token
ws.send(data)
b = s.post("https://discordapp.com/api/v6/channels/%s/messages" % channel, json=payload)
try:
    ws.close()
    print(1)
except:
    pass
    print(0)