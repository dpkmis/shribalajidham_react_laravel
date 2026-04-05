import React, { useState, useEffect } from 'react';
import { FaClock, FaMapMarkerAlt, FaArrowRight, FaChevronDown, FaOm, FaCamera, FaRoute, FaCalendarAlt, FaPray, FaMapMarkedAlt } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './Blog.css';

const iconMap = {
  FaOm: <FaOm />,
  FaPray: <FaPray />,
  FaMapMarkedAlt: <FaMapMarkedAlt />,
  FaCamera: <FaCamera />,
  FaRoute: <FaRoute />,
  FaCalendarAlt: <FaCalendarAlt />,
  FaMapMarkerAlt: <FaMapMarkerAlt />,
  FaClock: <FaClock />,
};

const fallbackArticles = [
  {
    icon: <FaOm />,
    title: 'Top 10 Temples to Visit in Mathura-Vrindavan',
    subtitle: 'Complete Darshan Guide for Pilgrims',
    readTime: '5 min read',
    image: 'https://images.pexels.com/photos/31626024/pexels-photo-31626024.jpeg?auto=compress&cs=tinysrgb&w=600',
    intro: 'Mathura-Vrindavan is the sacred land of Lord Krishna with hundreds of temples. Here are the top 10 must-visit temples for every pilgrim.',
    content: [
      { name: '1. Sri Krishna Janmabhoomi, Mathura', desc: 'The birthplace of Lord Krishna — the most sacred site in Mathura. Visit the prison cell where Krishna was born and the grand temple complex. Best time: Early morning for peaceful darshan. Distance from our hotel: 3.5 km.' },
      { name: '2. Dwarkadhish Temple, Mathura', desc: 'A magnificent 19th-century temple dedicated to Lord Krishna as King of Dwarka. Famous for the Swing Festival during Holi. The intricate carvings and paintings inside are breathtaking.' },
      { name: '3. Banke Bihari Temple, Vrindavan', desc: 'The most visited temple in Vrindavan, known for the enchanting idol of Lord Krishna. The curtain (jhulta parda) is drawn every few minutes. Extremely crowded during festivals — go early!' },
      { name: '4. ISKCON Temple, Vrindavan', desc: 'The grand Krishna Balaram Mandir built by ISKCON. Features beautiful architecture, melodious kirtans, and delicious prasad. A must-visit for international visitors.' },
      { name: '5. Prem Mandir, Vrindavan', desc: 'A stunning white marble temple with incredible light shows in the evening. Depicts scenes from Krishna\'s life. The evening light show (7:30 PM) is spectacular and free.' },
      { name: '6. Nidhivan, Vrindavan', desc: 'A sacred grove where Lord Krishna is believed to perform Raas Leela every night. The trees here have a unique formation. Closes at sunset — no one stays inside at night.' },
      { name: '7. Vishram Ghat, Mathura', desc: 'The most sacred ghat on the Yamuna where Lord Krishna rested after slaying Kansa. The evening Aarti ceremony here is mesmerizing. Don\'t miss it!' },
      { name: '8. Govardhan Hill (Govardhan Parikrama)', desc: '26 km circumambulation of the sacred Govardhan Hill that Lord Krishna lifted on his finger. Can be done by foot (6-8 hours) or by vehicle. We arrange guided parikrama tours.' },
      { name: '9. Radha Raman Temple, Vrindavan', desc: 'One of the oldest temples in Vrindavan (1542 AD). The self-manifested deity of Lord Krishna is unique — no artisan carved it. Maintained by Goswami families for centuries.' },
      { name: '10. Kusum Sarovar', desc: 'A beautiful stepped water tank between Govardhan and Radha Kund. Stunning architecture with cenotaphs. Less crowded — perfect for peaceful contemplation. Great for photography.' }
    ],
    tip: 'Stay at Shri BalaJi Dham Hotel for easy access to all these temples. We arrange guided darshan tours starting from ₹1,499.'
  },
  {
    icon: <FaRoute />,
    title: 'Mathura Vrindavan 2-Day Itinerary for Families',
    subtitle: 'Perfect Weekend Trip Plan',
    readTime: '4 min read',
    image: 'https://images.pexels.com/photos/20269174/pexels-photo-20269174.jpeg?auto=compress&cs=tinysrgb&w=600',
    intro: 'Planning a weekend trip to Mathura-Vrindavan with your family? Here\'s a perfect 2-day itinerary covering all major temples and attractions.',
    content: [
      { name: 'Day 1 — Mathura Darshan', desc: 'Morning: Arrive at Mathura Junction, check-in at Shri BalaJi Dham Hotel (10 min walk). After breakfast, visit Sri Krishna Janmabhoomi (10 AM). Afternoon: Dwarkadhish Temple, Mathura Museum, and local market shopping. Evening: Vishram Ghat evening Aarti (6:30 PM) — absolutely magical! Dinner at the hotel restaurant.' },
      { name: 'Day 2 — Vrindavan Temple Tour', desc: 'Early Morning: After breakfast, head to Vrindavan (25 min drive). Visit Banke Bihari Temple (go by 7 AM to avoid crowds). Then ISKCON Temple for darshan and prasad. Afternoon: Radha Raman Temple and Nidhivan. Evening: Prem Mandir light show (7:30 PM). Return to Mathura by 9 PM.' },
      { name: 'Budget Breakdown (Family of 4)', desc: 'Hotel (2 nights): ₹8,800 (Family Room) | Transport: ₹2,000-3,000 (AC cab for 2 days) | Meals: ₹2,000-3,000 | Temple donations: ₹500-1,000 | Shopping: ₹1,000-2,000. Total: approximately ₹15,000-18,000 for a comfortable family trip.' },
      { name: 'Pro Tips for Families', desc: 'Book hotel in advance especially during festivals. Carry comfortable walking shoes. Start early morning for temple visits (less crowd). Keep cash for temple donations and local shopping. Our hotel provides free WiFi to plan your itinerary and maps.' }
    ],
    tip: 'Book our Mathura Darshan + Vrindavan Tour combo package at ₹2,999/person — includes AC transport, guide, and all meals!'
  },
  {
    icon: <FaCalendarAlt />,
    title: 'Best Time to Visit Mathura-Vrindavan',
    subtitle: 'Season Guide & Festival Calendar',
    readTime: '3 min read',
    image: 'https://images.pexels.com/photos/15844430/pexels-photo-15844430.jpeg?auto=compress&cs=tinysrgb&w=600',
    intro: 'Mathura is a year-round destination, but each season offers a unique experience. Here\'s when to plan your visit for the best experience.',
    content: [
      { name: 'October to March (Best Season)', desc: 'The ideal time to visit! Pleasant weather (15-25°C) perfect for temple hopping and outdoor sightseeing. This is peak tourist and pilgrim season. Diwali (October/November) and Christmas-New Year are especially festive. Book hotels early!' },
      { name: 'February-March — Holi Festival', desc: 'Mathura and Vrindavan are FAMOUS for Holi! Lathmar Holi in Barsana, Phoolon ki Holi at Banke Bihari Temple, and the grand Holi celebration at Dwarkadheesh Temple make this the most colorful time. Book 2-3 months in advance!' },
      { name: 'August-September — Janmashtami', desc: 'The biggest festival in Mathura — Lord Krishna\'s birthday! The entire city transforms with lights, decorations, and 48-hour celebrations. Midnight darshan at Krishna Janmabhoomi is the highlight. Extremely crowded — book 3 months ahead!' },
      { name: 'April to June (Summer)', desc: 'Hot weather (35-45°C) but fewer crowds means easier darshan at popular temples. Many hotels offer summer discounts. If you can handle the heat, it\'s actually a good time for a quick pilgrimage. Our AC rooms provide cool comfort after temple visits.' },
      { name: 'July to September (Monsoon)', desc: 'The Yamuna fills up beautifully, and the ghats look spectacular. Jhullan Yatra and Janmashtami fall in this period. Occasional heavy rains may disrupt travel plans, so keep flexible itineraries.' }
    ],
    tip: 'Shri BalaJi Dham Hotel offers special packages during Holi, Janmashtami, and Diwali. Call +91 96390 66602 to check festival availability and rates!'
  },
  {
    icon: <FaCamera />,
    title: 'How to Reach Mathura from Delhi — Complete Guide',
    subtitle: 'By Train, Bus, Car & Flight',
    readTime: '3 min read',
    image: 'https://images.pexels.com/photos/30748404/pexels-photo-30748404.jpeg?auto=compress&cs=tinysrgb&w=600',
    intro: 'Mathura is well-connected to Delhi and other major cities. Here\'s a complete guide on how to reach Mathura from Delhi and other cities.',
    content: [
      { name: 'By Train from Delhi (Recommended)', desc: 'Fastest & most popular option! Trains from New Delhi/Nizamuddin to Mathura Junction run every 1-2 hours. Taj Express (2 hrs, ₹100-500), Bhopal Shatabdi (1.5 hrs, ₹300-800), and many Superfast trains. Book on IRCTC. Our hotel is just 10 min walk from Mathura Junction station.' },
      { name: 'By Car from Delhi (Via Yamuna Expressway)', desc: 'Drive 180 km via Yamuna Expressway — fastest route at 2.5-3 hours. Toll charges approximately ₹600-700. Alternatively, take NH-44 via Faridabad-Palwal (3.5-4 hours, less toll). Free parking at our hotel!' },
      { name: 'By Bus from Delhi', desc: 'UPSRTC and private buses run from Delhi ISBT Sarai Kale Khan to Mathura every 30 minutes. Journey takes 3-4 hours. Ticket: ₹200-400 for AC bus. Volvo/luxury buses also available. From Mathura Bus Stand, auto to our hotel costs ₹30-50.' },
      { name: 'By Flight + Car', desc: 'No direct flights to Mathura. Nearest airport is Agra Airport (57 km, limited flights from Delhi/Mumbai). Better option: Fly to Delhi IGI Airport, then take train/car to Mathura (3-3.5 hours). We arrange airport pick-up service.' }
    ],
    tip: 'Save time and hassle — share your travel details with us at +91 96390 66602 and we\'ll arrange pick-up from station, bus stand, or airport!'
  }
];

const Blog = () => {
  const [expandedArticle, setExpandedArticle] = useState(null);
  const [articles, setArticles] = useState(fallbackArticles);

  useEffect(() => {
    const fetchBlogPosts = async () => {
      try {
        const response = await publicAPI.blogPosts();
        const posts = response.data || response;
        if (Array.isArray(posts) && posts.length > 0) {
          const mapped = posts.map((post) => ({
            icon: iconMap[post.icon] || <FaOm />,
            title: post.title || '',
            subtitle: post.subtitle || '',
            readTime: post.read_time_min ? post.read_time_min + ' min read' : '3 min read',
            image: post.image || '',
            intro: post.excerpt || '',
            content: [
              {
                name: post.subtitle || post.title || '',
                desc: post.content || '',
              },
            ],
            tip: '',
          }));
          setArticles(mapped);
        }
      } catch (error) {
        // On error, keep fallback data
      }
    };
    fetchBlogPosts();
  }, []);

  const toggleArticle = (index) => {
    setExpandedArticle(expandedArticle === index ? null : index);
  };

  return (
    <section id="travel-guide" className="section blog-section">
      <div className="blog-bg-pattern"></div>
      <div className="container" style={{ position: 'relative', zIndex: 1 }}>
        <div className="section-header">
          <span className="section-subtitle">— Travel Guide —</span>
          <h2 className="section-title">Mathura Vrindavan Travel Guide</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Plan your perfect pilgrimage to the sacred land of Lord Krishna with our
            comprehensive travel guides, temple itineraries, and insider tips.
          </p>
        </div>

        <div className="blog-grid">
          {articles.map((article, i) => (
            <article key={i} className={`blog-card ${expandedArticle === i ? 'blog-expanded' : ''}`}>
              <div className="blog-card-image">
                <img src={article.image} alt={article.title} loading="lazy" />
                <div className="blog-read-time"><FaClock /> {article.readTime}</div>
              </div>
              <div className="blog-card-body">
                <div className="blog-card-icon">{article.icon}</div>
                <h3>{article.title}</h3>
                <span className="blog-subtitle">{article.subtitle}</span>
                <p className="blog-intro">{article.intro}</p>

                {expandedArticle === i && (
                  <div className="blog-full-content">
                    {article.content.map((item, j) => (
                      <div key={j} className="blog-content-item">
                        <h4>{item.name}</h4>
                        <p>{item.desc}</p>
                      </div>
                    ))}
                    <div className="blog-tip">
                      <FaMapMarkerAlt />
                      <p><strong>Shri BalaJi Dham Tip:</strong> {article.tip}</p>
                    </div>
                  </div>
                )}

                <button className="blog-read-more" onClick={() => toggleArticle(i)}>
                  {expandedArticle === i ? 'Show Less' : 'Read Full Guide'}
                  {expandedArticle === i ? <FaChevronDown style={{ transform: 'rotate(180deg)' }} /> : <FaArrowRight />}
                </button>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Blog;
