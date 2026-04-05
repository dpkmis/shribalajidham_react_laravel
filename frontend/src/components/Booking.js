import React, { useState, useEffect } from 'react';
import { FaCalendarAlt, FaUser, FaBed, FaPhone, FaEnvelope, FaCheckCircle, FaArrowRight, FaShieldAlt, FaTag, FaClock, FaSpinner, FaExclamationTriangle } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import { useSite } from '../context/SiteContext';
import './Booking.css';

const Booking = () => {
  const { contact } = useSite();
  const [formData, setFormData] = useState({
    name: '', email: '', phone: '', checkIn: '', checkOut: '',
    roomType: '', guests: '1', specialRequests: ''
  });
  const [roomTypes, setRoomTypes] = useState([]);
  const [submitted, setSubmitted] = useState(false);
  const [bookingResult, setBookingResult] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  // Fetch room types for the dropdown
  useEffect(() => {
    publicAPI.roomTypes()
      .then((res) => {
        if (res.success && res.data) {
          setRoomTypes(res.data);
        }
      })
      .catch(() => {
        // Fallback handled in render
      });
  }, []);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
    setError('');
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    setError('');

    try {
      const res = await publicAPI.createBooking({
        name: formData.name,
        phone: formData.phone,
        email: formData.email,
        check_in: formData.checkIn,
        check_out: formData.checkOut,
        room_type_id: parseInt(formData.roomType),
        guests: parseInt(formData.guests),
        special_requests: formData.specialRequests || null,
      });

      if (res.success) {
        setBookingResult(res.data);
        setSubmitted(true);
      } else {
        setError(res.message || 'Something went wrong. Please try again.');
      }
    } catch (err) {
      setError(err.message || `Unable to process booking. Please call ${contact.phone_display || '+91 96390 66602'} for assistance.`);
    } finally {
      setSubmitting(false);
    }
  };

  const formatPrice = (rate) => {
    return Number(rate).toLocaleString('en-IN');
  };

  return (
    <section id="booking" className="section booking-section">
      <div className="booking-bg-overlay"></div>
      <div className="container" style={{ position: 'relative', zIndex: 1 }}>
        <div className="booking-layout">
          <div className="booking-info">
            <span className="section-subtitle" style={{ color: 'var(--gold)' }}>— Reservation —</span>
            <h2 className="section-title" style={{ color: 'white', textAlign: 'left' }}>
              Book Your Stay at<br/>Shri BalaJi Dham, Mathura
            </h2>
            <div className="section-divider" style={{ margin: '16px 0 24px', marginLeft: 0 }}></div>
            <p className="booking-desc">
              Reserve your room at Mathura's most trusted budget hotel near the Railway Station.
              Enjoy spotlessly clean rooms, complimentary breakfast, and warm hospitality —
              just minutes from Krishna Janmabhoomi, Dwarkadhish Temple, and Vishram Ghat.
            </p>

            <div className="booking-perks">
              <div className="perk"><FaCheckCircle /> <span>Best Price — Direct Booking Guarantee</span></div>
              <div className="perk"><FaCheckCircle /> <span>Free Cancellation up to 24 Hours</span></div>
              <div className="perk"><FaCheckCircle /> <span>No Hidden Charges — Transparent Pricing</span></div>
              <div className="perk"><FaCheckCircle /> <span>Instant Booking Confirmation</span></div>
              <div className="perk"><FaCheckCircle /> <span>Complimentary Breakfast Included</span></div>
              <div className="perk"><FaCheckCircle /> <span>Free Railway Station Pick-up Available</span></div>
            </div>

            <div className="booking-policies">
              <div className="policy-item">
                <FaClock className="policy-icon" />
                <div>
                  <span className="policy-title">Check-In: 2:00 PM</span>
                  <span className="policy-sub">Check-Out: 12:00 PM</span>
                </div>
              </div>
              <div className="policy-item">
                <FaShieldAlt className="policy-icon" />
                <div>
                  <span className="policy-title">Smoke-Free Property</span>
                  <span className="policy-sub">Hygiene Plus Certified</span>
                </div>
              </div>
              <div className="policy-item">
                <FaTag className="policy-icon" />
                <div>
                  <span className="policy-title">Rooms from &#8377;2,376</span>
                  <span className="policy-sub">Best rates guaranteed</span>
                </div>
              </div>
            </div>

            <div className="booking-hotline">
              <p>For immediate assistance call:</p>
              <a href={`tel:${contact.phone}`} className="hotline-number">
                <FaPhone /> {contact.phone_display}
              </a>
            </div>
          </div>

          <div className="booking-form-wrap">
            {submitted && bookingResult ? (
              <div className="booking-success">
                <FaCheckCircle className="success-icon" />
                <h3>Booking Confirmed!</h3>
                <p style={{ fontSize: '1.1rem', fontWeight: '600', color: 'var(--gold)', margin: '8px 0' }}>
                  Reference: {bookingResult.booking_reference}
                </p>
                <div style={{ textAlign: 'left', background: '#f8f9fa', borderRadius: '8px', padding: '16px', margin: '16px 0' }}>
                  <p><strong>Guest:</strong> {bookingResult.guest_name}</p>
                  <p><strong>Room:</strong> {bookingResult.room_type}</p>
                  <p><strong>Check-In:</strong> {bookingResult.check_in}</p>
                  <p><strong>Check-Out:</strong> {bookingResult.check_out}</p>
                  <p><strong>Nights:</strong> {bookingResult.nights}</p>
                  <p><strong>Total:</strong> &#8377;{formatPrice(bookingResult.total_amount)}</p>
                </div>
                <p>{bookingResult.message}</p>
                <p style={{ marginTop: '12px', fontSize: '0.9rem', color: '#666' }}>
                  We will confirm your reservation within 2 hours via phone or email.
                </p>
                <button
                  className="btn-primary"
                  style={{ marginTop: '16px' }}
                  onClick={() => {
                    setSubmitted(false);
                    setBookingResult(null);
                    setFormData({ name: '', email: '', phone: '', checkIn: '', checkOut: '', roomType: '', guests: '1', specialRequests: '' });
                  }}
                >
                  Make Another Booking
                </button>
              </div>
            ) : (
              <form className="booking-form" onSubmit={handleSubmit}>
                <h3>Make a Reservation</h3>
                <p className="form-sub">Fill in the details below and we'll confirm your booking.</p>

                {error && (
                  <div style={{ background: '#fff3cd', border: '1px solid #ffc107', borderRadius: '8px', padding: '12px', marginBottom: '16px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <FaExclamationTriangle style={{ color: '#856404' }} />
                    <span style={{ color: '#856404', fontSize: '0.9rem' }}>{error}</span>
                  </div>
                )}

                <div className="form-row">
                  <div className="form-group">
                    <label><FaUser /> Full Name *</label>
                    <input type="text" name="name" placeholder="Enter your full name" value={formData.name} onChange={handleChange} required />
                  </div>
                  <div className="form-group">
                    <label><FaPhone /> Phone / WhatsApp *</label>
                    <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" value={formData.phone} onChange={handleChange} required />
                  </div>
                </div>

                <div className="form-group">
                  <label><FaEnvelope /> Email Address *</label>
                  <input type="email" name="email" placeholder="your@email.com" value={formData.email} onChange={handleChange} required />
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label><FaCalendarAlt /> Check-In Date *</label>
                    <input type="date" name="checkIn" value={formData.checkIn} onChange={handleChange} min={new Date().toISOString().split('T')[0]} required />
                  </div>
                  <div className="form-group">
                    <label><FaCalendarAlt /> Check-Out Date *</label>
                    <input type="date" name="checkOut" value={formData.checkOut} onChange={handleChange} min={formData.checkIn || new Date().toISOString().split('T')[0]} required />
                  </div>
                </div>

                <div className="form-row">
                  <div className="form-group">
                    <label><FaBed /> Room Type *</label>
                    <select name="roomType" value={formData.roomType} onChange={handleChange} required>
                      <option value="">Select a Room</option>
                      {roomTypes.length > 0 ? (
                        roomTypes.map((rt) => (
                          <option key={rt.id} value={rt.id}>
                            {rt.name} — &#8377;{formatPrice(rt.default_rate)}/night
                          </option>
                        ))
                      ) : (
                        <>
                          <option value="1">Standard Room — &#8377;2,376/night</option>
                          <option value="2">Deluxe Room — &#8377;3,520/night</option>
                          <option value="3">Family Room — &#8377;4,400/night</option>
                          <option value="4">Superior Double — &#8377;4,400/night</option>
                        </>
                      )}
                    </select>
                  </div>
                  <div className="form-group">
                    <label><FaUser /> Number of Guests</label>
                    <select name="guests" value={formData.guests} onChange={handleChange}>
                      <option value="1">1 Guest</option>
                      <option value="2">2 Guests</option>
                      <option value="3">3 Guests</option>
                      <option value="4">4 Guests</option>
                      <option value="5">5+ Guests</option>
                    </select>
                  </div>
                </div>

                <div className="form-group">
                  <label>Special Requests (Optional)</label>
                  <textarea name="specialRequests" placeholder="Temple darshan assistance, early check-in, extra bed, dietary needs..." rows="3" value={formData.specialRequests} onChange={handleChange}></textarea>
                </div>

                <button type="submit" className="btn-primary booking-submit-btn" disabled={submitting}>
                  {submitting ? (
                    <><FaSpinner className="fa-spin" /> Processing...</>
                  ) : (
                    <>Confirm Reservation <FaArrowRight /></>
                  )}
                </button>
              </form>
            )}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Booking;
