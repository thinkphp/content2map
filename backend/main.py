from flask import Flask, request, jsonify
from flask_cors import CORS
import spacy
import requests
from geopy.geocoders import Nominatim
from geopy.exc import GeocoderTimedOut

app = Flask(__name__)
CORS(app)

# Load the English language model
nlp = spacy.load("en_core_web_sm")

# Initialize the geocoder
geolocator = Nominatim(user_agent="location_analyzer")

def get_coordinates(location_name):
    try:
        location = geolocator.geocode(location_name)
        if location:
            return {
                "name": location_name,
                "latitude": location.latitude,
                "longitude": location.longitude
            }
    except GeocoderTimedOut:
        pass
    return None

@app.route('/api/analyze', methods=['POST'])
def analyze_text():
    data = request.json
    text = data.get('text', '')
    
    # Process the text with spaCy
    doc = nlp(text)
    
    # Extract locations (GPE = Geo-Political Entity)
    locations = []
    seen_locations = set()
    
    for ent in doc.ents:
        if ent.label_ in ['GPE', 'LOC'] and ent.text not in seen_locations:
            location_data = get_coordinates(ent.text)
            if location_data:
                locations.append(location_data)
                seen_locations.add(ent.text)
    
    return jsonify({"locations": locations})

if __name__ == '__main__':
    app.run(debug=True)
