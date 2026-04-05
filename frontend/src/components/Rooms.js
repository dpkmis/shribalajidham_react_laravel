import React, { useState, useEffect } from 'react';
import { FaWifi, FaSnowflake, FaTv, FaBath, FaParking, FaConciergeBell, FaCheck, FaArrowRight, FaRulerCombined, FaUsers, FaBed, FaCoffee, FaSpinner } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './Rooms.css';

// Fallback data if API is unavailable
const fallbackRooms = [
  {
    name: 'Standard Room', price: '2,376',
    image: 'https://images.pexels.com/photos/5461582/pexels-photo-5461582.jpeg?auto=compress&cs=tinysrgb&w=600',
    description: 'Our cozy Standard Room is ideal for solo travellers and couples visiting Mathura-Vrindavan for darshan.',
    size: '180 sq.ft', guests: '2 Adults', bed: 'Twin / Double Bed',
    amenities: ['Free WiFi', 'Air Conditioning', 'LED TV', 'Hot Water', 'Room Service', 'Free Parking'],
    features: ['Daily Housekeeping', 'Fresh Linen & Towels', 'Private Bathroom with Shower', 'Complimentary Breakfast'],
    tag: 'Value Stay'
  },
  {
    name: 'Deluxe Room', price: '3,520',
    image: 'https://images.pexels.com/photos/16197244/pexels-photo-16197244.jpeg?auto=compress&cs=tinysrgb&w=600',
    description: 'Step into our premium Deluxe Room with elegant interiors, AC, wardrobe, and wall-mounted TV panel.',
    size: '280 sq.ft', guests: '2 Adults + 1 Child', bed: 'King Size Bed',
    amenities: ['High-Speed WiFi', 'Air Conditioning', 'Flat Screen TV', 'Balcony', 'Room Service', 'Free Parking'],
    features: ['Balcony with View', 'Premium Bathroom Amenities', 'Complimentary Breakfast', 'Mini Fridge', 'Daily Housekeeping'],
    tag: 'Most Booked'
  },
  {
    name: 'Family Room', price: '4,400',
    image: process.env.PUBLIC_URL + '/images/room-family.jpeg',
    description: 'Spacious interconnected room designed for families on their Braj Bhoomi yatra.',
    size: '380 sq.ft', guests: '2 Adults + 2 Children', bed: 'King + Extra Bedding',
    amenities: ['High-Speed WiFi', 'Air Conditioning', 'Smart TV', 'Hot Water', '24/7 Service', 'Free Parking'],
    features: ['Interconnected Rooms', 'Extra Bedding Options', 'Complimentary Breakfast', 'Private Bathroom', 'Ample Luggage Space', 'Daily Housekeeping'],
    tag: 'Family Favourite'
  },
  {
    name: 'Superior Double Room', price: '4,400',
    image: 'https://images.pexels.com/photos/18703869/pexels-photo-18703869.jpeg?auto=compress&cs=tinysrgb&w=600',
    description: 'Our finest room featuring a double bed and king bed combination, elegant furnishings, and superior amenities.',
    size: '350 sq.ft', guests: '3 Adults', bed: 'Double + King Bed',
    amenities: ['High-Speed WiFi', 'Air Conditioning', 'Smart TV', 'Premium Bath', '24/7 Service', 'Free Parking'],
    features: ['Premium Furnishings', 'Spacious Layout', 'All Meals Available', 'Complimentary Breakfast', 'Lockers Available', 'Daily Housekeeping'],
    tag: 'Premium Choice'
  }
];

const formatPrice = (rate) => {
  return Number(rate).toLocaleString('en-IN');
};

const fallbackImages = {
  'Standard': 'https://images.pexels.com/photos/5461582/pexels-photo-5461582.jpeg?auto=compress&cs=tinysrgb&w=600',
  'Deluxe': 'https://images.pexels.com/photos/16197244/pexels-photo-16197244.jpeg?auto=compress&cs=tinysrgb&w=600',
  'Family': process.env.PUBLIC_URL + '/images/room-family.jpeg',
  'Superior': 'https://images.pexels.com/photos/18703869/pexels-photo-18703869.jpeg?auto=compress&cs=tinysrgb&w=600',
};

const getDefaultImage = (name) => {
  const key = Object.keys(fallbackImages).find(k => name.toLowerCase().includes(k.toLowerCase()));
  return key ? fallbackImages[key] : fallbackImages['Standard'];
};

const mapApiToRoom = (rt) => {
  const featureNames = (rt.features || []).map(f => f.name);
  return {
    id: rt.id,
    name: rt.name,
    price: formatPrice(rt.default_rate),
    image: (rt.images && rt.images.length > 0) ? rt.images[0] : getDefaultImage(rt.name),
    description: rt.description || '',
    size: rt.room_size_sqm ? `${Math.round(rt.room_size_sqm * 10.764)} sq.ft` : 'N/A',
    guests: rt.max_adults && rt.max_children
      ? `${rt.max_adults} Adults${rt.max_children > 0 ? ` + ${rt.max_children} Children` : ''}`
      : `${rt.max_occupancy} Guests`,
    bed: rt.bed_type ? rt.bed_type.charAt(0).toUpperCase() + rt.bed_type.slice(1) + ' Bed' : 'Double Bed',
    amenities: featureNames.length > 0 ? featureNames : (rt.amenities || ['Free WiFi', 'Air Conditioning', 'LED TV']),
    features: ['Complimentary Breakfast', 'Daily Housekeeping', 'Fresh Linen & Towels'],
    tag: rt.available_rooms_count > 0 ? `${rt.available_rooms_count} Available` : 'On Request',
    available_rooms_count: rt.available_rooms_count,
    total_rooms_count: rt.total_rooms_count,
  };
};

const Rooms = () => {
  const [activeRoom, setActiveRoom] = useState(0);
  const [rooms, setRooms] = useState(fallbackRooms);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    publicAPI.roomTypes()
      .then((res) => {
        if (res.success && res.data && res.data.length > 0) {
          setRooms(res.data.map(mapApiToRoom));
        }
      })
      .catch(() => {
        // Keep fallback data
      })
      .finally(() => setLoading(false));
  }, []);

  const amenityIcons = {
    'Free WiFi': <FaWifi />, 'High-Speed WiFi': <FaWifi />, 'WIFI': <FaWifi />,
    'Air Conditioning': <FaSnowflake />, 'AC': <FaSnowflake />,
    'LED TV': <FaTv />, 'Flat Screen TV': <FaTv />, 'Smart TV': <FaTv />,
    'Room Service': <FaConciergeBell />, '24/7 Service': <FaConciergeBell />,
    'Free Parking': <FaParking />,
    'Hot Water': <FaBath />, 'Premium Bath': <FaBath />,
    'Balcony': <FaCoffee />,
    'Daily Housekeeping': <FaCheck />,
    'Complimentary Breakfast': <FaCoffee />,
    'CCTV Security': <FaCheck />,
    'Safe Deposit Lockers': <FaCheck />,
    'Mini Fridge': <FaSnowflake />,
    'Premium Bedding': <FaBed />,
    'Pick-up & Drop': <FaCheck />,
    'Temple Tour Guidance': <FaCheck />,
  };

  return (
    <section id="rooms" className="section rooms-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Accommodations —</span>
          <h2 className="section-title">Clean &amp; Comfortable Rooms</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Every room at Shri BalaJi Dham is meticulously cleaned and well-maintained to ensure
            a hygienic and restful stay for pilgrims visiting the holy city of Mathura and Vrindavan.
          </p>
        </div>

        {loading ? (
          <div style={{ textAlign: 'center', padding: '60px 0' }}>
            <FaSpinner className="fa-spin" style={{ fontSize: '2rem', color: 'var(--gold)' }} />
            <p style={{ marginTop: '12px', color: '#888' }}>Loading rooms...</p>
          </div>
        ) : (
          <>
            <div className="rooms-nav">
              {rooms.map((room, i) => (
                <button
                  key={i}
                  className={`room-nav-btn ${activeRoom === i ? 'active' : ''}`}
                  onClick={() => setActiveRoom(i)}
                >
                  <span className="room-nav-name">{room.name}</span>
                  <span className="room-nav-price">from &#8377;{room.price}/night</span>
                </button>
              ))}
            </div>

            <div className="room-showcase">
              <div className="room-showcase-img">
                <img src={rooms[activeRoom].image} alt={`${rooms[activeRoom].name} at Shri Balaji Dham Hotel Mathura`} loading="lazy" />
                <div className="room-tag">{rooms[activeRoom].tag}</div>
                <div className="room-price-badge">
                  <span className="rpb-label">Starting from</span>
                  <span className="rpb-amount">&#8377;{rooms[activeRoom].price}</span>
                  <span className="rpb-per">per night + taxes</span>
                </div>
              </div>

              <div className="room-showcase-info">
                <h3>{rooms[activeRoom].name}</h3>
                <p className="room-showcase-desc">{rooms[activeRoom].description}</p>

                <div className="room-specs-row">
                  <div className="room-spec-item">
                    <FaRulerCombined className="spec-icon" />
                    <div>
                      <span className="spec-key">Size</span>
                      <span className="spec-val">{rooms[activeRoom].size}</span>
                    </div>
                  </div>
                  <div className="room-spec-item">
                    <FaUsers className="spec-icon" />
                    <div>
                      <span className="spec-key">Capacity</span>
                      <span className="spec-val">{rooms[activeRoom].guests}</span>
                    </div>
                  </div>
                  <div className="room-spec-item">
                    <FaBed className="spec-icon" />
                    <div>
                      <span className="spec-key">Bed Type</span>
                      <span className="spec-val">{rooms[activeRoom].bed}</span>
                    </div>
                  </div>
                </div>

                <div className="room-amenities-section">
                  <h4>Room Amenities</h4>
                  <div className="room-amenities-row">
                    {rooms[activeRoom].amenities.map((a, i) => (
                      <span key={i} className="room-amenity-chip">
                        {amenityIcons[a] || <FaCheck />} {a}
                      </span>
                    ))}
                  </div>
                </div>

                <div className="room-features-section">
                  <h4>What&apos;s Included</h4>
                  <ul className="room-features-list">
                    {rooms[activeRoom].features.map((f, i) => (
                      <li key={i}><FaCheck className="feat-check" /> {f}</li>
                    ))}
                  </ul>
                </div>

                <a href="#booking" className="btn-primary room-cta">
                  Book {rooms[activeRoom].name} <FaArrowRight />
                </a>
              </div>
            </div>
          </>
        )}
      </div>
    </section>
  );
};

export default Rooms;
