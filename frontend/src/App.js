import React, { Suspense, lazy } from 'react';
import { HelmetProvider } from 'react-helmet-async';
import { SiteProvider } from './context/SiteContext';
import SeoHead from './components/SeoHead';
import Navbar from './components/Navbar';
import Hero from './components/Hero';
import About from './components/About';
import Rooms from './components/Rooms';
import './App.css';

const Amenities = lazy(() => import('./components/Amenities'));
const TourPackages = lazy(() => import('./components/TourPackages'));
const FestivalOffers = lazy(() => import('./components/FestivalOffers'));
const Gallery = lazy(() => import('./components/Gallery'));
const NearbyAttractions = lazy(() => import('./components/NearbyAttractions'));
const Testimonials = lazy(() => import('./components/Testimonials'));
const GoogleRating = lazy(() => import('./components/GoogleRating'));
const Blog = lazy(() => import('./components/Blog'));
const HowToReach = lazy(() => import('./components/HowToReach'));
const FAQ = lazy(() => import('./components/FAQ'));
const Booking = lazy(() => import('./components/Booking'));
const Contact = lazy(() => import('./components/Contact'));
const Footer = lazy(() => import('./components/Footer'));
const WhatsAppButton = lazy(() => import('./components/WhatsAppButton'));

const SectionLoader = () => (
  <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', padding: '60px 20px', color: '#E85D04', fontFamily: 'Poppins, sans-serif', fontSize: '0.9rem' }}>
    Loading...
  </div>
);

function App() {
  return (
    <HelmetProvider>
    <SiteProvider>
      <SeoHead />
      <div className="App">
        <Navbar />
        <Hero />
        <About />
        <Rooms />
        <Suspense fallback={<SectionLoader />}>
          <Amenities />
          <TourPackages />
          <FestivalOffers />
          <Gallery />
          <NearbyAttractions />
          <Testimonials />
          <GoogleRating />
          <Blog />
          <HowToReach />
          <FAQ />
          <Booking />
          <Contact />
          <Footer />
          <WhatsAppButton />
        </Suspense>
      </div>
    </SiteProvider>
    </HelmetProvider>
  );
}

export default App;
