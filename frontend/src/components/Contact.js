import React from 'react';
import { FaMapMarkerAlt, FaPhone, FaEnvelope, FaClock, FaWhatsapp, FaFacebookF, FaInstagram, FaTwitter, FaYoutube, FaDirections, FaTrain } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './Contact.css';

const Contact = () => {
  const { contact, social, policies } = useSite();

  return (
    <section id="contact" className="section contact-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Get In Touch —</span>
          <h2 className="section-title">Contact Us</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Have questions about room availability, Mathura darshan, or travel assistance?
            We are here to help you plan the perfect pilgrimage stay.
          </p>
        </div>

        <div className="contact-layout">
          <div className="contact-cards">
            <div className="ccard">
              <div className="ccard-icon"><FaMapMarkerAlt /></div>
              <div><h4>Hotel Address</h4><p>{contact.address}</p></div>
            </div>
            <div className="ccard">
              <div className="ccard-icon"><FaPhone /></div>
              <div><h4>Phone / WhatsApp</h4><p><a href={`tel:${contact.phone}`}>{contact.phone_display}</a></p></div>
            </div>
            <div className="ccard">
              <div className="ccard-icon"><FaEnvelope /></div>
              <div><h4>Email</h4><p><a href={`mailto:${contact.email}`}>{contact.email}</a></p></div>
            </div>
            <div className="ccard">
              <div className="ccard-icon"><FaClock /></div>
              <div><h4>Check-In / Check-Out</h4><p>Check-In: {policies.checkin_time}<br/>Check-Out: {policies.checkout_time}</p></div>
            </div>
            <div className="ccard">
              <div className="ccard-icon"><FaTrain /></div>
              <div><h4>Nearest Railway Station</h4><p>{policies.nearest_railway}<br/>Pick-up service available on request</p></div>
            </div>
            <div className="ccard">
              <div className="ccard-icon"><FaDirections /></div>
              <div><h4>Nearest Airport</h4><p>{policies.nearest_airport}</p></div>
            </div>
          </div>

          <div className="contact-map">
            <iframe
              title="Shri Balaji Dham Hotel Mathura Location"
              src={contact.google_maps_embed || "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3534.5!2d77.6737!3d27.5046"}
              width="100%" height="100%"
              style={{ border: 0, borderRadius: 'var(--radius-lg)' }}
              allowFullScreen="" loading="lazy" referrerPolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>

        <div className="contact-social-strip">
          <h4>Connect With Us</h4>
          <div className="social-icons">
            {social.facebook && <a href={social.facebook} target="_blank" rel="noopener noreferrer" className="soc-icon soc-fb" aria-label="Facebook"><FaFacebookF /></a>}
            {social.instagram && <a href={social.instagram} target="_blank" rel="noopener noreferrer" className="soc-icon soc-ig" aria-label="Instagram"><FaInstagram /></a>}
            {social.twitter && <a href={social.twitter} target="_blank" rel="noopener noreferrer" className="soc-icon soc-tw" aria-label="Twitter"><FaTwitter /></a>}
            {social.youtube && <a href={social.youtube} target="_blank" rel="noopener noreferrer" className="soc-icon soc-yt" aria-label="YouTube"><FaYoutube /></a>}
            <a href={`https://wa.me/${contact.whatsapp}`} target="_blank" rel="noopener noreferrer" className="soc-icon soc-wa" aria-label="WhatsApp"><FaWhatsapp /></a>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Contact;
