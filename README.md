# locations2map

Extract Geographical Locations and Automatically links them to open maps.

![content2map](https://github.com/thinkphp/content2map/blob/master/Screenshot%20from%202025-02-07%2011-42-12.png?raw=true)

To set up and run this application:

1. Set up the Flask backend:
```bash
pip install flask flask-cors spacy geopy
python -m spacy download en_core_web_sm
```

2. Set up the React frontend:
```bash
# Install required dependencies
```

3. Run both applications:
```bash
# Run Flask backend (in one terminal)
python app.py

# Run Next.js frontend (in another terminal)
npm run dev
```

Key features I've added:
1. Backend:
   - Uses spaCy for named entity recognition to find locations
   - Geocodes locations using Nominatim
   - Handles CORS for frontend communication
   - Deduplicates locations

2. Frontend:
   - Proper map cleanup on component unmount
   - Error handling and loading states
   - Responsive design using Tailwind CSS
   - Automatic map bounds adjustment to show all locations
   - Interactive markers with popups
   - Input validation
