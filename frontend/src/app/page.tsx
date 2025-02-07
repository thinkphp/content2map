'use client';

import React, { useState, useEffect, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Loader2 } from 'lucide-react';
import dynamic from 'next/dynamic';
import 'leaflet/dist/leaflet.css';

// Define a Map type to avoid TypeScript errors
type Map = any;
type LayerGroup = any;

const LocationAnalyzer = () => {
  const [text, setText] = useState('');
  const [locations, setLocations] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [mapLoaded, setMapLoaded] = useState(false);
  const mapContainer = useRef(null);
  const map = useRef<Map>(null);
  const markersLayer = useRef<LayerGroup>(null);
  const L = useRef<any>(null);

  useEffect(() => {
    if (typeof window !== 'undefined' && !L.current) {
      Promise.all([
        import('leaflet')
      ]).then(([leaflet]) => {
        L.current = leaflet;
        const DefaultIcon = L.current.icon({
          iconUrl: '/leaflet/marker-icon.png',
          iconRetinaUrl: '/leaflet/marker-icon-2x.png',
          shadowUrl: '/leaflet/marker-shadow.png',
          iconSize: [25, 41],
          iconAnchor: [12, 41],
          popupAnchor: [1, -34],
          shadowSize: [41, 41]
        });
        L.current.Marker.prototype.options.icon = DefaultIcon;
        setMapLoaded(true);
      });
    }

    return () => {
      if (map.current) {
        map.current.remove();
        map.current = null;
      }
    };
  }, []);

  const analyzeText = async () => {
    if (!text.trim()) {
      setError('Please enter some text to analyze');
      return;
    }

    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch('http://localhost:5000/api/analyze', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text }),
      });
      
      if (!response.ok) {
        throw new Error('Analysis failed. Please try again.');
      }

      const data = await response.json();
      setLocations(data.locations);
      
      if (data.locations.length > 0 && mapLoaded) {
        initializeMap(data.locations);
      } else if (data.locations.length === 0) {
        setError('No locations found in the text');
      }
    } catch (error: any) {
      setError(error.message);
      console.error('Error analyzing text:', error);
    } finally {
      setLoading(false);
    }
  };
  
  const initializeMap = (locations: any[]) => {
    if (!L.current || !mapContainer.current) return;

    // If map doesn't exist, create it
    if (!map.current) {
      map.current = L.current.map(mapContainer.current).setView([0, 0], 2);
      L.current.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(map.current);
    }

    // Clear existing markers
    if (markersLayer.current) {
      markersLayer.current.clearLayers();
    } else {
      markersLayer.current = L.current.layerGroup().addTo(map.current);
    }

    // Add new markers
    const bounds = L.current.latLngBounds([]);
    
    locations.forEach((location) => {
      const marker = L.current.marker([location.latitude, location.longitude])
        .bindPopup(location.name);
      markersLayer.current.addLayer(marker);
      bounds.extend([location.latitude, location.longitude]);
    });

    // Fit map to show all markers with a slight delay to ensure proper rendering
    if (locations.length > 0) {
      setTimeout(() => {
        map.current?.invalidateSize();
        map.current?.fitBounds(bounds, { padding: [50, 50] });
      }, 250);
    }
  };

  // Effect to update map when locations change
  useEffect(() => {
    if (locations.length > 0 && mapLoaded) {
      initializeMap(locations);
    }
  }, [locations, mapLoaded]);

  return (
    <div className="max-w-4xl mx-auto p-4">
      <Card className="mb-4">
        <CardHeader>
          <CardTitle>Location Analyzer</CardTitle>
        </CardHeader>
        <CardContent>
          <textarea
            className="w-full p-2 border rounded-md mb-4 min-h-32"
            value={text}
            onChange={(e) => setText(e.target.value)}
            placeholder="Enter text containing location names (e.g., 'I visited Paris and then went to London.')"
            disabled={loading}
          />
          <button
            className="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:bg-blue-300 flex items-center gap-2"
            onClick={analyzeText}
            disabled={loading}
          >
            {loading && <Loader2 className="h-4 w-4 animate-spin" />}
            {loading ? 'Analyzing...' : 'Analyze Text'}
          </button>
          
          {error && (
            <Alert variant="destructive" className="mt-4">
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          )}
        </CardContent>
      </Card>

      {locations.length > 0 && (
        <>
          <Card className="mb-4">
            <CardHeader>
              <CardTitle>Found Locations</CardTitle>
            </CardHeader>
            <CardContent>
              <ul className="space-y-2">
                {locations.map((location, index) => (
                  <li key={index} className="flex items-center gap-2">
                    <span className="font-medium">{location.name}</span>
                    <span className="text-gray-500 text-sm">
                      ({location.latitude.toFixed(4)}, {location.longitude.toFixed(4)})
                    </span>
                  </li>
                ))}
              </ul>
            </CardContent>
          </Card>

          <Card>
            <CardContent>
              <div ref={mapContainer} className="h-96 w-full rounded-md" />
            </CardContent>
          </Card>
        </>
      )}
    </div>
  );
};

export default LocationAnalyzer;
