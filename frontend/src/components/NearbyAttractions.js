import React, { useState, useEffect } from 'react';
import { FaMapMarkerAlt, FaClock, FaRoute } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './NearbyAttractions.css';

const fallbackAttractions = [
  {
    name: 'Sri Krishna Janmabhoomi',
    distance: '3.5 km',
    time: '10 min drive',
    image: 'https://images.pexels.com/photos/31626024/pexels-photo-31626024.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'The sacred birthplace of Lord Krishna — the most important pilgrimage destination in Mathura and the entire Braj region. A must-visit for every devotee.',
    highlights: []
  },
  {
    name: 'Dwarkadhish Temple',
    distance: '3.8 km',
    time: '12 min drive',
    image: 'https://images.pexels.com/photos/7104962/pexels-photo-7104962.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'A magnificent 19th-century temple in Mathura dedicated to Lord Krishna as King of Dwarka, known for intricate architecture and vibrant festivals.',
    highlights: []
  },
  {
    name: 'Vishram Ghat',
    distance: '3.5 km',
    time: '10 min drive',
    image: 'https://images.pexels.com/photos/30210504/pexels-photo-30210504.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'The most sacred ghat on the Yamuna in Mathura, where Lord Krishna rested after slaying Kansa. Famous for the mesmerizing evening Aarti ceremony.',
    highlights: []
  },
  {
    name: 'Mathura Junction Railway Station',
    distance: '800 m',
    time: '10 min walk',
    image: 'https://images.pexels.com/photos/15670045/pexels-photo-15670045.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'Our hotel is conveniently located just 10 minutes walking distance from Mathura Junction, making arrival and departure completely hassle-free.',
    highlights: []
  },
  {
    name: 'Sai Baba Temple',
    distance: '110 m',
    time: '2 min walk',
    image: 'https://images.pexels.com/photos/28257150/pexels-photo-28257150.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'The nearest temple to our hotel, Sai Baba Temple is just steps away from Shri Balaji Dham — perfect for a quick morning darshan.',
    highlights: []
  },
  {
    name: 'Vrindavan Temples',
    distance: '12 km',
    time: '25 min drive',
    image: 'https://images.pexels.com/photos/16228271/pexels-photo-16228271.jpeg?auto=compress&cs=tinysrgb&w=500',
    desc: 'Visit Banke Bihari Temple, ISKCON, Prem Mandir, and Nidhivan in Vrindavan — easily accessible as a day trip from our Mathura hotel.',
    highlights: []
  },
];

const NearbyAttractions = () => {
  const [attractions, setAttractions] = useState(fallbackAttractions);

  useEffect(() => {
    const fetchAttractions = async () => {
      try {
        const response = await publicAPI.nearbyAttractions();
        const data = response.data || response;
        if (Array.isArray(data) && data.length > 0) {
          const mapped = data.map((item) => ({
            name: item.name,
            distance: item.distance,
            time: item.travel_time,
            image: item.image,
            desc: item.description,
            highlights: item.highlights || [],
          }));
          setAttractions(mapped);
        }
      } catch (error) {
        console.error('Failed to fetch nearby attractions:', error);
      }
    };
    fetchAttractions();
  }, []);

  return (
    <section className="section attractions-section">
      <div className="attractions-bg"></div>
      <div className="container" style={{ position: 'relative', zIndex: 1 }}>
        <div className="section-header">
          <span className="section-subtitle">— Explore Nearby —</span>
          <h2 className="section-title">Sacred Places Near Our Hotel</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Shri BalaJi Dham is perfectly situated in Dhauli Pyau, Mathura — close to
            the railway station and all major temples, ghats, and pilgrimage sites.
          </p>
        </div>
        <div className="attractions-grid">
          {attractions.map((a, i) => (
            <div key={i} className="attract-card">
              <div className="attract-img">
                <img src={a.image} alt={`${a.name} near Shri Balaji Dham Hotel Mathura`} loading="lazy" />
                <div className="attract-dist-badge"><FaRoute /> {a.distance}</div>
              </div>
              <div className="attract-body">
                <h3>{a.name}</h3>
                <p>{a.desc}</p>
                <div className="attract-meta">
                  <span><FaMapMarkerAlt /> {a.distance} from hotel</span>
                  <span><FaClock /> {a.time}</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default NearbyAttractions;
