import React, { useState, useEffect } from 'react';
import { FaTimes, FaChevronLeft, FaChevronRight, FaExpand } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './Gallery.css';

const fallbackImages = [
  { url: process.env.PUBLIC_URL + '/images/hotel-entrance.jpeg', caption: 'Hotel Entrance — Neon Signage & Aquarium Lobby', category: 'Hotel' },
  { url: process.env.PUBLIC_URL + '/images/room-family.jpeg', caption: 'Family Room — Spacious Twin Bed with AC', category: 'Rooms' },
  { url: 'https://images.pexels.com/photos/5461582/pexels-photo-5461582.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Standard Room — Clean & Comfortable', category: 'Rooms' },
  { url: process.env.PUBLIC_URL + '/images/hotel-corridor.jpeg', caption: 'Hotel Corridor — Spotless Marble Flooring', category: 'Hotel' },
  { url: 'https://images.pexels.com/photos/16197244/pexels-photo-16197244.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Deluxe Room — AC, Wardrobe & TV Panel', category: 'Rooms' },
  { url: 'https://images.pexels.com/photos/31626024/pexels-photo-31626024.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Krishna Janmabhoomi Temple, Mathura', category: 'Mathura' },
  { url: 'https://images.pexels.com/photos/30210504/pexels-photo-30210504.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Evening Aarti at Sacred Ghats, Mathura', category: 'Mathura' },
  { url: 'https://images.pexels.com/photos/1603650/pexels-photo-1603650.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Taj Mahal — Agra Day Trip from Mathura', category: 'Tours' },
];

const Gallery = () => {
  const [lightbox, setLightbox] = useState({ open: false, index: 0 });
  const [images, setImages] = useState(fallbackImages);

  useEffect(() => {
    publicAPI.gallery()
      .then(res => {
        const data = res.data;
        if (Array.isArray(data) && data.length > 0) {
          const mapped = data.map(item => ({
            url: item.image,
            caption: item.title + (item.caption ? ' — ' + item.caption : ''),
            category: item.category,
          }));
          setImages(mapped);
        }
      })
      .catch(() => {
        // Keep fallback images on error
      });
  }, []);

  const openLightbox = (index) => setLightbox({ open: true, index });
  const closeLightbox = () => setLightbox({ open: false, index: 0 });
  const navigate = (dir) => {
    setLightbox(prev => ({
      ...prev,
      index: (prev.index + dir + images.length) % images.length
    }));
  };

  return (
    <section id="gallery" className="section gallery-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Photo Gallery —</span>
          <h2 className="section-title">A Glimpse of Our Property</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            From our welcoming neon-lit entrance to spotlessly clean rooms and shining
            marble corridors — see the real Shri BalaJi Dham Hotel, Mathura.
          </p>
        </div>
        <div className="gallery-mosaic">
          {images.map((img, i) => (
            <div
              key={i}
              className={`gallery-tile ${i === 0 ? 'tile-large' : ''}`}
              onClick={() => openLightbox(i)}
            >
              <img src={img.url} alt={img.caption} loading="lazy" />
              <div className="tile-overlay">
                <span className="tile-category">{img.category}</span>
                <span className="tile-caption">{img.caption}</span>
                <FaExpand className="tile-expand" />
              </div>
            </div>
          ))}
        </div>
      </div>

      {lightbox.open && (
        <div className="lightbox-backdrop" onClick={closeLightbox}>
          <button className="lb-close" onClick={closeLightbox}><FaTimes /></button>
          <button className="lb-nav lb-prev" onClick={(e) => { e.stopPropagation(); navigate(-1); }}><FaChevronLeft /></button>
          <div className="lb-content" onClick={(e) => e.stopPropagation()}>
            <img src={images[lightbox.index].url} alt={images[lightbox.index].caption} />
            <p className="lb-caption">{images[lightbox.index].caption}</p>
          </div>
          <button className="lb-nav lb-next" onClick={(e) => { e.stopPropagation(); navigate(1); }}><FaChevronRight /></button>
          <div className="lb-counter">{lightbox.index + 1} / {images.length}</div>
        </div>
      )}
    </section>
  );
};

export default Gallery;
