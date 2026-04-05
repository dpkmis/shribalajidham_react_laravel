import React from 'react';
import { FaMapMarkerAlt, FaStar, FaPlay, FaArrowRight } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './Hero.css';

const Hero = () => {
  const { general, contact, hero } = useSite();

  const parseStat = (stat) => {
    if (!stat) return { value: '', label: '' };
    const parts = stat.split(' ');
    return { value: parts[0], label: parts.slice(1).join(' ') };
  };

  const s1 = parseStat(hero.hero_stat_1);
  const s2 = parseStat(hero.hero_stat_2);
  const s3 = parseStat(hero.hero_stat_3);
  const s4 = parseStat(hero.hero_stat_4);

  return (
    <section id="home" className="hero" role="banner" style={{ backgroundImage: hero.hero_image ? `url(${hero.hero_image})` : `url(${process.env.PUBLIC_URL}/images/hotel-entrance.png)` }}>
      <div className="hero-bg-overlay"></div>
      <div className="hero-golden-particles">
        {[...Array(15)].map((_, i) => (
          <span key={i} className="g-particle" style={{
            left: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 6}s`,
            animationDuration: `${4 + Math.random() * 5}s`,
            opacity: 0.3 + Math.random() * 0.4
          }}></span>
        ))}
      </div>

      <div className="container hero-content">
        <div className="hero-badge-row">
          <div className="hero-badge">
            <FaStar />
            <span>{general.hero_badge}</span>
            <FaStar />
          </div>
        </div>

        <h1 className="hero-heading">
          <span className="hero-welcome">Welcome to</span>
          <span className="hero-brand">{general.site_name?.replace(' Hotel', '')}</span>
          <span className="hero-sub-brand">Hotel in Mathura</span>
        </h1>

        <p className="hero-tagline-text">
          &ldquo; {general.tagline} &rdquo;
        </p>

        <div className="hero-location-badge">
          <FaMapMarkerAlt />
          <span>{contact.address_short}</span>
        </div>

        <p className="hero-description">
          {hero.hero_subtitle || general.short_description}
        </p>

        <div className="hero-cta">
          <a href="#booking" className="btn-primary hero-btn">
            Book Your Stay <FaArrowRight />
          </a>
          <a href="#rooms" className="btn-outline hero-btn-outline">
            <FaPlay className="play-icon" /> Explore Rooms
          </a>
        </div>

        <div className="hero-trust-strip">
          <div className="trust-item">
            <span className="trust-value">{s1.value}</span>
            <span className="trust-label">{s1.label}</span>
          </div>
          <div className="trust-divider"></div>
          <div className="trust-item">
            <span className="trust-value">{s2.value}<FaStar className="trust-star" /></span>
            <span className="trust-label">{s2.label}</span>
          </div>
          <div className="trust-divider"></div>
          <div className="trust-item">
            <span className="trust-value">{s3.value}</span>
            <span className="trust-label">{s3.label}</span>
          </div>
          <div className="trust-divider"></div>
          <div className="trust-item">
            <span className="trust-value">{s4.value}</span>
            <span className="trust-label">{s4.label}</span>
          </div>
        </div>
      </div>

      <div className="hero-scroll-indicator">
        <span>Scroll</span>
        <div className="scroll-line">
          <div className="scroll-dot"></div>
        </div>
      </div>

      <div className="hero-bottom-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 720,0 1080,60 C1260,90 1380,80 1440,60 L1440,120 L0,120 Z" fill="var(--cream)"/>
        </svg>
      </div>
    </section>
  );
};

export default Hero;
