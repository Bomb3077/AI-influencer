import json

PINECONE_API_KEY = ""
with open('api_key.txt', 'r') as file:
    data = json.load(file)
    PINECONE_API_KEY = data['pinecone']

if PINECONE_API_KEY == "": raise RuntimeError("pinecone api key not found")
