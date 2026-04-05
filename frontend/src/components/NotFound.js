import React from 'react';
import { FaHome, FaPhone } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';

const NotFound = () => {
  const { contact } = useSite();

  return (
    <div style={{
      minHeight: '80vh', display: 'flex', flexDirection: 'column',
      alignItems: 'center', justifyContent: 'center', textAlign: 'center',
      padding: '40px 20px', fontFamily: 'Poppins, sans-serif',
    }}>
      <div style={{ fontSize: '6rem', fontWeight: '700', color: '#E85D04', lineHeight: 1 }}>404</div>
      <h2 style={{ margin: '16px 0 8px', color: '#1a1a2e' }}>Page Not Found</h2>
      <p style={{ color: '#666', maxWidth: '400px', marginBottom: '24px' }}>
        The page you're looking for doesn't exist. Let us help you find your way to a comfortable stay in Mathura.
      </p>
      <div style={{ display: 'flex', gap: '12px', flexWrap: 'wrap', justifyContent: 'center' }}>
        <a href="/" style={{
          display: 'inline-flex', alignItems: 'center', gap: '8px',
          padding: '12px 24px', background: '#E85D04', color: '#fff',
          borderRadius: '8px', textDecoration: 'none', fontWeight: '600',
        }}>
          <FaHome /> Go to Homepage
        </a>
        <a href={`tel:${contact.phone}`} style={{
          display: 'inline-flex', alignItems: 'center', gap: '8px',
          padding: '12px 24px', border: '2px solid #E85D04', color: '#E85D04',
          borderRadius: '8px', textDecoration: 'none', fontWeight: '600',
        }}>
          <FaPhone /> Call {contact.phone_display}
        </a>
      </div>
    </div>
  );
};

export default NotFound;
