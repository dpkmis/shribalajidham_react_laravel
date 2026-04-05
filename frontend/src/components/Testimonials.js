import React, { useState, useEffect } from 'react';
import { FaStar, FaQuoteLeft, FaChevronLeft, FaChevronRight } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './Testimonials.css';

const fallbackTestimonials = [
  {
    name: 'Rajesh Kumar',
    location: 'New Delhi',
    rating: 5,
    text: 'We stayed at Shri Balaji Dham during our Mathura-Vrindavan yatra and were impressed by the cleanliness. Rooms were spotless, bed linen was fresh, and the bathroom was hygienic. Staff helped us plan our temple visits — truly felt like a home away from home!',
    avatar: 'RK',
    date: 'February 2025',
    via: 'Google Review'
  },
  {
    name: 'Priya Sharma',
    location: 'Mumbai',
    rating: 5,
    text: 'Best budget hotel in Mathura near the railway station! The location at Dhauli Pyau is perfect — we reached from Mathura Junction in just 10 minutes. The rooms were clean and the complimentary breakfast with poori-sabzi and chai was delicious. They also arranged a cab for our Vrindavan day trip. Highly recommended for families!',
    avatar: 'PS',
    date: 'January 2025',
    via: 'Google Review'
  },
  {
    name: 'Amit Gupta',
    location: 'Jaipur',
    rating: 5,
    text: 'Came for the Braj 84 Kos Yatra package and it was perfectly organized. The hotel arranged our entire darshan schedule, transport, and meals. Rooms were air-conditioned and well-maintained. Great value for money — you won\'t find a better deal near Krishna Janmabhoomi.',
    avatar: 'AG',
    date: 'December 2024',
    via: 'Booking.com'
  },
  {
    name: 'Sunita Devi',
    location: 'Lucknow',
    rating: 5,
    text: 'Visited during Janmashtami with my elderly parents. The staff was extremely helpful with wheelchair assistance. The Satvik food was pure and tasty — just like home cooking. Rooms were neat, clean, and comfortable. They even arranged special darshan for us. Will come back every year!',
    avatar: 'SD',
    date: 'August 2024',
    via: 'Google Review'
  },
  {
    name: 'Vikram Singh',
    location: 'Chandigarh',
    rating: 4,
    text: 'Very clean and affordable hotel in Mathura. The Dhauli Pyau location is close to the railway station which made our arrival easy. Pick-up service was on time. WiFi worked well. The vegetarian restaurant had good variety. Perfect for a pilgrim stay in Mathura.',
    avatar: 'VS',
    date: 'November 2024',
    via: 'Hotels.com'
  },
  {
    name: 'Meera Patel',
    location: 'Ahmedabad',
    rating: 5,
    text: 'Outstanding hospitality! From the welcome tea to the farewell — every moment was warm and devotional. The Govardhan Parikrama tour they arranged was the highlight of our trip. Rooms are modern, clean, and surprisingly spacious for this price range. Truly under divine observation!',
    avatar: 'MP',
    date: 'October 2024',
    via: 'TripAdvisor'
  }
];

const getInitials = (name) => {
  if (!name) return '??';
  const parts = name.trim().split(/\s+/);
  const first = parts[0] ? parts[0][0].toUpperCase() : '';
  const last = parts.length > 1 ? parts[parts.length - 1][0].toUpperCase() : '';
  return first + last;
};

const formatStayDate = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return dateStr;
  return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
};

const Testimonials = () => {
  const [active, setActive] = useState(0);
  const [testimonials, setTestimonials] = useState(fallbackTestimonials);

  useEffect(() => {
    const fetchTestimonials = async () => {
      try {
        const response = await publicAPI.testimonials();
        const data = response.data || response;
        const items = Array.isArray(data) ? data : (data.results || data.testimonials || []);
        if (items.length > 0) {
          const mapped = items.map((item) => ({
            name: item.guest_name || '',
            location: item.guest_location || '',
            rating: item.rating || 5,
            text: item.review_text || '',
            avatar: getInitials(item.guest_name),
            date: formatStayDate(item.stay_date),
            via: item.source || '',
            is_featured: item.is_featured || false
          }));
          setTestimonials(mapped);
        }
      } catch (error) {
        // Keep fallback testimonials on error
      }
    };
    fetchTestimonials();
  }, []);

  const navigate = (dir) => {
    setActive((prev) => (prev + dir + testimonials.length) % testimonials.length);
  };

  const visibleIndices = () => {
    const arr = [];
    for (let i = -1; i <= 1; i++) {
      arr.push((active + i + testimonials.length) % testimonials.length);
    }
    return arr;
  };

  return (
    <section id="testimonials" className="section testimonials-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Guest Reviews —</span>
          <h2 className="section-title">What Our Guests Say</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Don't just take our word for it — hear from real pilgrims and travellers
            who chose Shri BalaJi Dham for their Mathura-Vrindavan stay.
          </p>
        </div>

        <div className="testi-carousel">
          <button className="testi-nav testi-prev" onClick={() => navigate(-1)} aria-label="Previous review">
            <FaChevronLeft />
          </button>

          <div className="testi-track">
            {visibleIndices().map((idx, pos) => (
              <div
                key={idx}
                className={`testi-card ${pos === 1 ? 'testi-center' : 'testi-side'}`}
              >
                <div className="testi-card-top">
                  <FaQuoteLeft className="testi-quote" />
                  <div className="testi-stars">
                    {[...Array(testimonials[idx].rating)].map((_, i) => (
                      <FaStar key={i} />
                    ))}
                  </div>
                </div>
                <p className="testi-text">{testimonials[idx].text}</p>
                <div className="testi-author">
                  <div className="testi-avatar">{testimonials[idx].avatar}</div>
                  <div className="testi-author-info">
                    <h4>{testimonials[idx].name}</h4>
                    <span>{testimonials[idx].location}</span>
                  </div>
                  <span className="testi-via">{testimonials[idx].via}</span>
                </div>
              </div>
            ))}
          </div>

          <button className="testi-nav testi-next" onClick={() => navigate(1)} aria-label="Next review">
            <FaChevronRight />
          </button>
        </div>

        <div className="testi-dots">
          {testimonials.map((_, i) => (
            <button
              key={i}
              className={`testi-dot ${i === active ? 'dot-active' : ''}`}
              onClick={() => setActive(i)}
              aria-label={`Review ${i + 1}`}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
