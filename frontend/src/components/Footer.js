import React from 'react';
import { FaMapMarkerAlt, FaPhone, FaEnvelope, FaHeart, FaArrowUp, FaOm, FaWhatsapp } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './Footer.css';

const Footer = () => {
  const { general, contact } = useSite();
  const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

  return (
    <footer className="footer">
      <div className="footer-wave">
        <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
          <path d="M0,40 C480,100 960,0 1440,40 L1440,0 L0,0 Z" fill="var(--cream)"/>
        </svg>
      </div>

      <div className="container">
        <div className="footer-grid">
          <div className="footer-brand">
            <div className="footer-logo-row">
              {general.logo ? (
                <img src={general.logo} alt={general.site_name || 'Shri BalaJi Dham'} className="footer-logo-img" />
              ) : (                
                <div></div>
              )}
             
            </div>
            <p className="footer-desc">{general.short_description}</p>
            <FaOm className="footer-om" />
          </div>

          <div className="footer-col">
            <h4>Quick Links</h4>
            <ul>
              <li><a href="#home">Home</a></li>
              <li><a href="#about">About Us</a></li>
              <li><a href="#rooms">Rooms &amp; Suites</a></li>
              <li><a href="#packages">Tour Packages</a></li>
              <li><a href="#festival-offers">Festival Packages</a></li>
              <li><a href="#gallery">Photo Gallery</a></li>
              <li><a href="#testimonials">Guest Reviews</a></li>
              <li><a href="#travel-guide">Travel Guide</a></li>
              <li><a href="#faq">FAQ</a></li>
              <li><a href="#booking">Book Now</a></li>
            </ul>
          </div>

          <div className="footer-col">
            <h4>Our Services</h4>
            <ul>
              <li><a href="#rooms">Standard Rooms</a></li>
              <li><a href="#rooms">Deluxe Rooms</a></li>
              <li><a href="#rooms">Family Rooms</a></li>
              <li><a href="#packages">Mathura Darshan Tour</a></li>
              <li><a href="#packages">Vrindavan Temple Tour</a></li>
              <li><a href="#festival-offers">Janmashtami Package</a></li>
              <li><a href="#festival-offers">Holi Package</a></li>
              <li><a href="#how-to-reach">How to Reach</a></li>
              <li><a href="#amenities">Pure Veg Restaurant</a></li>
            </ul>
          </div>

          <div className="footer-col footer-contact-col">
            <h4>Contact</h4>
            <div className="fc-item"><FaMapMarkerAlt /><span>{contact.address_short}</span></div>
            <div className="fc-item"><FaPhone /><a href={`tel:${contact.phone}`}>{contact.phone_display}</a></div>
            <div className="fc-item"><FaWhatsapp /><a href={`https://wa.me/${contact.whatsapp}`} target="_blank" rel="noopener noreferrer">WhatsApp Us</a></div>
            <div className="fc-item"><FaEnvelope /><a href={`mailto:${contact.email}`}>{contact.email}</a></div>
          </div>
        </div>

        <div className="footer-bottom">
          <p>&copy; {new Date().getFullYear()} {general.site_name}, Mathura. All Rights Reserved. Made with <FaHeart className="fb-heart" /> in Mathura</p>
          <div className="fb-links">
            <a href="#!">Privacy Policy</a>
            <a href="#!">Terms &amp; Conditions</a>
            <a href="#!">Cancellation Policy</a>
          </div>
        </div>
      </div>

      <button className="scroll-top-btn" onClick={scrollToTop} aria-label="Scroll to top"><FaArrowUp /></button>
    </footer>
  );
};

export default Footer;
