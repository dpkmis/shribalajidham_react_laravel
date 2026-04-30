import React from 'react';
import { FaWhatsapp } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './WhatsAppButton.css';

const WhatsAppButton = () => {
  const { contact, booking } = useSite();
  const msg = encodeURIComponent(booking.whatsapp_booking_msg || 'Namaste! I would like to book a room.');

  return (
    <a
      href={`https://wa.me/${contact.whatsapp || '918755550410'}?text=${msg}`}
      className="wa-float"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Book via WhatsApp"
    >
      <FaWhatsapp />
      <span className="wa-tooltip">Book on WhatsApp</span>
    </a>
  );
};

export default WhatsAppButton;
