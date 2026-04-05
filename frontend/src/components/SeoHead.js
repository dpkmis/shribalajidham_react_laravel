import { Helmet } from 'react-helmet-async';
import { useSite } from '../context/SiteContext';

const SeoHead = () => {
  const { meta, general, contact, hero, booking } = useSite();
  const seo = meta.seo || {};

  const title = seo.meta_title || `${general.site_name} — ${general.hero_badge}`;
  const description = seo.meta_description || general.short_description || '';
  const keywords = seo.meta_keywords || '';
  const ogImage = seo.og_image || hero.hero_image || '';
  const siteUrl = 'https://shribalajidhamhotel.com';
  const gaId = seo.google_analytics || '';

  // Build dynamic JSON-LD from CMS data
  const hotelSchema = {
    '@context': 'https://schema.org',
    '@type': 'Hotel',
    name: general.site_name,
    description: general.short_description,
    url: siteUrl,
    telephone: contact.phone,
    email: contact.email,
    address: {
      '@type': 'PostalAddress',
      streetAddress: contact.address,
      addressLocality: 'Mathura',
      addressRegion: 'Uttar Pradesh',
      postalCode: '281001',
      addressCountry: 'IN',
    },
    geo: { '@type': 'GeoCoordinates', latitude: '27.4946', longitude: '77.6737' },
    starRating: { '@type': 'Rating', ratingValue: '3' },
    aggregateRating: { '@type': 'AggregateRating', ratingValue: '4.0', reviewCount: '150', bestRating: '5' },
    priceRange: `₹${booking.min_rate || '2376'} - ₹4,400`,
    currenciesAccepted: 'INR',
    checkinTime: meta.policies?.checkin_time || '14:00',
    checkoutTime: meta.policies?.checkout_time || '12:00',
    amenityFeature: [
      { '@type': 'LocationFeatureSpecification', name: 'Free WiFi', value: true },
      { '@type': 'LocationFeatureSpecification', name: 'Free Parking', value: true },
      { '@type': 'LocationFeatureSpecification', name: 'Air Conditioning', value: true },
      { '@type': 'LocationFeatureSpecification', name: 'Complimentary Breakfast', value: true },
      { '@type': 'LocationFeatureSpecification', name: '24/7 Front Desk', value: true },
      { '@type': 'LocationFeatureSpecification', name: 'Temple Tour Guidance', value: true },
      { '@type': 'LocationFeatureSpecification', name: 'Railway Station Pickup', value: true },
    ],
  };

  return (
    <Helmet>
      <title>{title}</title>
      <meta name="description" content={description} />
      {keywords && <meta name="keywords" content={keywords} />}
      <link rel="canonical" href={siteUrl} />

      {/* Open Graph */}
      <meta property="og:title" content={title} />
      <meta property="og:description" content={description} />
      <meta property="og:type" content="website" />
      <meta property="og:url" content={siteUrl} />
      <meta property="og:site_name" content={general.site_name} />
      <meta property="og:locale" content="en_IN" />
      {ogImage && <meta property="og:image" content={ogImage} />}

      {/* Twitter */}
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content={title} />
      <meta name="twitter:description" content={description} />
      {ogImage && <meta name="twitter:image" content={ogImage} />}

      {/* JSON-LD Structured Data */}
      <script type="application/ld+json">{JSON.stringify(hotelSchema)}</script>

      {/* Google Analytics */}
      {gaId && (
        <script async src={`https://www.googletagmanager.com/gtag/js?id=${gaId}`}></script>
      )}
      {gaId && (
        <script>{`window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','${gaId}');`}</script>
      )}
    </Helmet>
  );
};

export default SeoHead;
