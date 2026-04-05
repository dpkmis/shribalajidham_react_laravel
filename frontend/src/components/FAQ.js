import React, { useState } from 'react';
import { FaChevronDown, FaQuestionCircle } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './FAQ.css';

const FAQ = () => {
  const [openIndex, setOpenIndex] = useState(null);
  const { contact, booking } = useSite();
  const phone = contact.phone_display || '+91 96390 66602';
  const wa = contact.whatsapp || '919639066602';

  const faqs = [
    {
      question: 'How far is Shri Balaji Dham Hotel from Mathura Junction Railway Station?',
      answer: `Shri Balaji Dham Hotel is just 800 meters (approximately 10 minutes walk) from Mathura Junction Railway Station. We also offer complimentary pick-up service from the station on request. Just call us at ${phone} when you arrive!`
    },
    {
      question: 'What is the room price at Shri Balaji Dham Hotel Mathura?',
      answer: `Our room prices are very affordable: Standard Room starts at ${booking.currency || '₹'}2,376/night (180 sq.ft), Deluxe Room at ${booking.currency || '₹'}3,520/night (280 sq.ft with balcony), Family Room at ${booking.currency || '₹'}4,400/night (380 sq.ft, interconnected), and Superior Double Room at ${booking.currency || '₹'}4,400/night (350 sq.ft). All rooms include complimentary breakfast, free WiFi, AC, and LED TV.`
    },
    {
      question: 'Does the hotel offer temple darshan tour packages?',
      answer: 'Yes! We offer 4 curated packages: Mathura Darshan (₹1,499 for 1 day covering Krishna Janmabhoomi, Dwarkadhish Temple, Vishram Ghat), Vrindavan Temple Tour (₹1,799 for 1 day — most popular), Braj 84 Kos Yatra (₹5,999 for 3 days/2 nights — complete spiritual circuit), and Agra-Mathura Heritage Tour (₹3,999 for 2 days — includes Taj Mahal). All packages include AC transport, expert guide, and meals.'
    },
    {
      question: 'What are the check-in and check-out times?',
      answer: 'Standard check-in time is 2:00 PM and check-out time is 12:00 PM (noon). Early check-in and late check-out may be available on request, subject to room availability. For pilgrims arriving by early morning trains, we offer luggage storage facility so you can start your darshan immediately.'
    },
    {
      question: 'Is breakfast included in the room price?',
      answer: 'Yes, complimentary breakfast is included with all room types. We serve fresh, pure vegetarian Satvik meals including poori-sabzi, paratha, chai, juice, and other local and continental breakfast options. Our in-house restaurant also serves lunch and dinner at reasonable prices.'
    },
    {
      question: 'How to reach Shri Balaji Dham Hotel from Delhi?',
      answer: 'From Delhi, you can reach Mathura in multiple ways: By Train — Take any train to Mathura Junction (2-3 hours), then walk 10 minutes or use our free pick-up. By Road — Mathura is 180 km from Delhi via Yamuna Expressway (2.5-3 hours by car). By Air — The nearest airport is Agra Airport (Kheria), about 57 km away (1.5 hours by car). We can arrange pick-up from all locations.'
    },
    {
      question: 'Which temples are near the hotel?',
      answer: 'Several sacred temples are nearby: Sai Baba Temple (110m, 2 min walk), Sri Krishna Janmabhoomi (3.5 km, 10 min drive), Dwarkadhish Temple (3.8 km, 12 min), Vishram Ghat (3.5 km, 10 min). Vrindavan temples — ISKCON, Banke Bihari, Prem Mandir, Nidhivan — are 12 km away (25 min drive). Our staff can arrange transport and guide for all temple visits.'
    },
    {
      question: 'Does the hotel have parking and WiFi?',
      answer: 'Yes! We offer free on-site parking for cars, bikes, and traveller buses with CCTV surveillance. Free high-speed WiFi is available throughout the entire property — in rooms, lobby, and restaurant.'
    },
    {
      question: 'Is the hotel suitable for families with elderly members and children?',
      answer: 'Absolutely! Our Family Rooms (380 sq.ft) feature interconnected rooms with extra bedding. For elderly guests, we provide wheelchair assistance, ground-floor rooms on request, and can arrange special darshan schedules. Our pure vegetarian restaurant serves hygienic, home-style meals suitable for all ages. The hotel is smoke-free for a safe environment.'
    },
    {
      question: 'What payment methods are accepted?',
      answer: 'We accept all major payment methods: Cash, UPI (Google Pay, PhonePe, Paytm), Credit Cards (Visa, Mastercard), Debit Cards, and bank transfers. Online payment is available for advance bookings. No hidden charges — all prices are transparent and inclusive.'
    },
    {
      question: 'Can I book the hotel for Holi or Janmashtami celebrations in Mathura?',
      answer: `Yes, Mathura is famous for its grand Holi and Janmashtami celebrations! We recommend booking well in advance (2-3 months) for festival dates as rooms fill up quickly. We offer special festival packages with darshan schedules timed to festival events. Contact us at ${phone} or WhatsApp to check availability for festival dates.`
    },
    {
      question: 'Does the hotel provide railway station pick-up service?',
      answer: 'Yes, we offer complimentary pick-up service from Mathura Junction Railway Station (800m away). Simply share your train details and arrival time when booking, and our representative will be waiting at the station to guide you. We also provide paid pick-up from Agra Airport and other locations.'
    }
  ];

  const toggleFAQ = (index) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  return (
    <section id="faq" className="section faq-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Frequently Asked Questions —</span>
          <h2 className="section-title">Everything You Need to Know</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Planning your Mathura-Vrindavan pilgrimage? Here are answers to the most common
            questions about staying at Shri BalaJi Dham Hotel.
          </p>
        </div>

        <div className="faq-grid">
          {faqs.map((faq, i) => (
            <div
              key={i}
              className={`faq-item ${openIndex === i ? 'faq-open' : ''}`}
              onClick={() => toggleFAQ(i)}
              role="button"
              tabIndex={0}
              aria-expanded={openIndex === i}
              onKeyDown={(e) => e.key === 'Enter' && toggleFAQ(i)}
            >
              <div className="faq-question">
                <FaQuestionCircle className="faq-q-icon" />
                <h3>{faq.question}</h3>
                <FaChevronDown className={`faq-arrow ${openIndex === i ? 'faq-arrow-open' : ''}`} />
              </div>
              <div className={`faq-answer ${openIndex === i ? 'faq-answer-show' : ''}`}>
                <p>{faq.answer}</p>
              </div>
            </div>
          ))}
        </div>

        <div className="faq-cta">
          <p>Still have questions? We're happy to help!</p>
          <div className="faq-cta-buttons">
            <a href={`tel:${contact.phone}`} className="btn-primary">
              Call {phone}
            </a>
            <a href={`https://wa.me/${wa}?text=Hi%2C%20I%20have%20a%20question%20about%20Shri%20Balaji%20Dham%20Hotel%20Mathura`} target="_blank" rel="noopener noreferrer" className="btn-outline">
              WhatsApp Us
            </a>
          </div>
        </div>
      </div>
    </section>
  );
};

export default FAQ;
