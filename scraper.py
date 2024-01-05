import requests

url = "https://rocketapi-for-instagram.p.rapidapi.com/instagram/search"

payload = { "query": "kanyewest" }
headers = {
	"content-type": "application/json",
	"X-RapidAPI-Key": "02628f4b2amsh899f79a70368dd6p1a4caajsnc9777ca18573",
	"X-RapidAPI-Host": "rocketapi-for-instagram.p.rapidapi.com"
}

response = requests.post(url, json=payload, headers=headers)

print(response.json())