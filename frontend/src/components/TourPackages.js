import React, { useState, useEffect } from 'react';
import { FaCheck, FaClock, FaMapMarkerAlt, FaUsers, FaStar, FaArrowRight } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './TourPackages.css';

const fallbackPackages = [
  {
    name: 'Mathura Darshan',
    duration: '1 Day',
    price: '1,499',
    image: 'https://images.pexels.com/photos/31626024/pexels-photo-31626024.jpeg?auto=compress&cs=tinysrgb&w=600',
    popular: false,
    places: 'Mathura City',
    groupSize: '2-10',
    description: 'Explore the birthplace of Lord Krishna in a single memorable day.',
    includes: [
      'Sri Krishna Janmabhoomi Temple',
      'Dwarkadhish Temple Darshan',
      'Vishram Ghat Visit & Aarti',
      'Kusum Sarovar',
      'Mathura Museum Tour',
      'AC Transport & Guide',
      'Vegetarian Lunch Included'
    ]
  },
  {
    name: 'Vrindavan Temple Tour',
    duration: '1 Day',
    price: '1,799',
    image: 'https://images.pexels.com/photos/16228271/pexels-photo-16228271.jpeg?auto=compress&cs=tinysrgb&w=600',
    popular: true,
    places: 'Vrindavan',
    groupSize: '2-15',
    description: 'Visit the most sacred temples of Vrindavan with expert local guides.',
    includes: [
      'Banke Bihari Temple Darshan',
      'ISKCON Temple & Prasad',
      'Prem Mandir (Light Show)',
      'Nidhivan Sacred Grove',
      'Radha Raman Temple',
      'Seva Kunj & Kesi Ghat',
      'AC Transport & All Meals'
    ]
  },
  {
    name: 'Braj 84 Kos Yatra',
    duration: '3 Days / 2 Nights',
    price: '5,999',
    image: 'https://images.pexels.com/photos/30210504/pexels-photo-30210504.jpeg?auto=compress&cs=tinysrgb&w=600',
    popular: false,
    places: 'Mathura, Vrindavan, Govardhan, Barsana',
    groupSize: '4-20',
    description: 'The complete spiritual circuit of Braj Bhoomi covering all sacred sites.',
    includes: [
      'All Major Temples & Ghats',
      'Govardhan Parikrama',
      'Barsana & Nandgaon Visit',
      'Radha Kund Darshan',
      'Hotel Stay at Shri Balaji Dham, Mathura (2N)',
      'All Meals Included',
      'AC Transport & Expert Guide'
    ]
  },
  {
    name: 'Agra-Mathura Heritage',
    duration: '2 Days / 1 Night',
    price: '3,999',
    image: 'https://images.pexels.com/photos/33777272/pexels-photo-33777272.jpeg?auto=compress&cs=tinysrgb&w=600',
    popular: false,
    places: 'Mathura & Agra',
    groupSize: '2-10',
    description: 'Combine the spiritual essence of Mathura with the Mughal grandeur of Agra.',
    includes: [
      'Taj Mahal Sunrise Visit',
      'Agra Fort Heritage Tour',
      'Krishna Janmabhoomi Darshan',
      'Vrindavan Temple Circuit',
      'Hotel Stay at Shri Balaji Dham, Mathura (1N)',
      'All Meals & AC Transport',
      'Professional Heritage Guide'
    ]
  }
];

const TourPackages = () => {
  const [packages, setPackages] = useState(fallbackPackages);

  useEffect(() => {
    const fetchPackages = async () => {
      try {
        const response = await publicAPI.tourPackages();
        const data = response.data;
        if (data && data.length > 0) {
          const mapped = data.map((pkg) => ({
            name: pkg.name,
            duration: pkg.duration,
            price: Number(pkg.price).toLocaleString('en-IN'),
            image: pkg.image,
            popular: !!pkg.is_popular,
            places: pkg.places_covered,
            groupSize: pkg.group_size,
            description: pkg.price_label,
            includes: pkg.includes
          }));
          setPackages(mapped);
        }
      } catch (error) {
        // Keep fallback data on error
      }
    };
    fetchPackages();
  }, []);

  return (
    <section id="packages" className="section packages-section">
      <div className="packages-overlay"></div>
      <div className="container" style={{ position: 'relative', zIndex: 1 }}>
        <div className="section-header">
          <span className="section-subtitle" style={{ color: 'var(--gold)' }}>— Explore Braj Bhoomi —</span>
          <h2 className="section-title" style={{ color: 'white' }}>Curated Tour Packages</h2>
          <div className="section-divider"></div>
          <p className="section-description" style={{ color: 'rgba(255,255,255,0.6)' }}>
            Discover the divine lands of Mathura, Vrindavan, Govardhan, and Barsana with our
            thoughtfully designed tour packages — complete with darshan, meals, and comfortable transport.
          </p>
        </div>

        <div className="packages-grid">
          {packages.map((pkg, i) => (
            <div key={i} className={`pkg-card ${pkg.popular ? 'pkg-popular' : ''}`}>
              {pkg.popular && (
                <div className="pkg-popular-tag"><FaStar /> Most Popular</div>
              )}
              <div className="pkg-image">
                <img src={pkg.image} alt={`${pkg.name} - Mathura Vrindavan Tour Package`} loading="lazy" />
                <div className="pkg-duration"><FaClock /> {pkg.duration}</div>
              </div>
              <div className="pkg-body">
                <h3>{pkg.name}</h3>
                <p className="pkg-desc">{pkg.description}</p>
                <div className="pkg-meta">
                  <span><FaMapMarkerAlt /> {pkg.places}</span>
                  <span><FaUsers /> {pkg.groupSize} People</span>
                </div>
                <ul className="pkg-includes">
                  {pkg.includes.map((item, j) => (
                    <li key={j}><FaCheck /> {item}</li>
                  ))}
                </ul>
                <div className="pkg-footer">
                  <div className="pkg-price">
                    <span className="pkg-price-label">Starting from</span>
                    <span className="pkg-price-val">&#8377;{pkg.price}</span>
                    <span className="pkg-price-per">/ person</span>
                  </div>
                  <a href="#booking" className="btn-primary pkg-btn">
                    Book Now <FaArrowRight />
                  </a>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TourPackages;
