import React, { useState, useEffect } from 'react';
import { FaBars, FaTimes, FaPhone, FaEnvelope, FaMapMarkerAlt, FaWhatsapp } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './Navbar.css';

const Navbar = () => {
  const { contact, general } = useSite();
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 60);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const closeMenu = () => setIsOpen(false);

  const navLinks = [
    { name: 'Home', href: '#home' },
    { name: 'About', href: '#about' },
    { name: 'Rooms', href: '#rooms' },
    { name: 'Packages', href: '#packages' },
    { name: 'Gallery', href: '#gallery' },
    { name: 'Reviews', href: '#testimonials' },
    { name: 'Guide', href: '#travel-guide' },
    { name: 'FAQ', href: '#faq' },
    { name: 'Contact', href: '#contact' },
  ];

  return (
    <>
      <div className="top-bar">
        <div className="container top-bar-inner">
          <div className="top-bar-left">
            <a href={`tel:${contact.phone}`} className="top-bar-item">
              <FaPhone /> <span>{contact.phone_display}</span>
            </a>
            <a href={`mailto:${contact.email}`} className="top-bar-item">
              <FaEnvelope /> <span>{contact.email}</span>
            </a>
          </div>
          <div className="top-bar-right">
            <span className="top-bar-item">
              <FaMapMarkerAlt /> <span>{contact.address_short}</span>
            </span>
            <a href={`https://wa.me/${contact.whatsapp}`} target="_blank" rel="noopener noreferrer" className="top-bar-whatsapp">
              <FaWhatsapp /> WhatsApp
            </a>
          </div>
        </div>
      </div>

      <nav className={`navbar ${scrolled ? 'scrolled' : ''}`} role="navigation" aria-label="Main Navigation">
        <div className="container navbar-inner">
          <a href="#home" className="logo" aria-label="Shri BalaJi Dham Hotel Mathura - Home">
            {general.logo ? (
              <img src={general.logo} alt={general.site_name || 'Shri BalaJi Dham'} className="logo-img" />
            ) : (
              <div className="logo-mark">
                <span className="logo-shri">श्री</span>
              </div>
            )}
          
          </a>

          <div className={`nav-menu ${isOpen ? 'active' : ''}`}>
            <div className="nav-menu-header">
              {general.logo ? (
                <img src={general.logo} alt={general.site_name || 'Shri BalaJi Dham'} className="mobile-logo-img" />
              ) : (
                <span className="mobile-logo-text">Shri BalaJi Dham</span>
              )}
              <button className="nav-close" onClick={closeMenu} aria-label="Close menu"><FaTimes /></button>
            </div>
            {navLinks.map((link, i) => (
              <a key={i} href={link.href} className="nav-link" onClick={closeMenu}>{link.name}</a>
            ))}
            <a href="#booking" className="btn-book" onClick={closeMenu}>Book Now</a>
          </div>

          {isOpen && <div className="nav-overlay" onClick={closeMenu}></div>}

          <button className="nav-toggle" onClick={() => setIsOpen(!isOpen)} aria-label="Toggle menu">
            <FaBars />
          </button>
        </div>
      </nav>
    </>
  );
};

export default Navbar;
