import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

const aboutContent = `
<p>MallFinder is your premier destination for discovering the exploring, and experiencing the best shopping destinations. Founded with a passion for retail excellence, we have grown to become one of the most comprehensive shopping mall directories available.</p>

<h2>Our Mission</h2>
<p>Our mission is to connect shoppers with the perfect shopping experiences. We believe that every shopping trip should be enjoyable, convenient, and memorable. Whether you're looking for luxury brands, everyday essentials, or unique finds, we're here to guide you to the right destination.</p>

<h2>What We Offer</h2>
<p>We provide detailed information about shopping malls across various locations, including:</p>
<ul>
  <li><strong>Comprehensive Directory:</strong> Browse through our extensive collection of shopping malls, complete with essential details like addresses, operating hours, and contact information.</li>
  <li><strong>Store Listings:</strong> Explore the stores within each mall, categorized by type for easy navigation - from fashion and electronics to dining and entertainment.</li>
  <li><strong>Interactive Maps:</strong> Find malls near you with our integrated mapping system, making it easy to plan your shopping trips.</li>
  <li><strong>Real-time Updates:</strong> Stay informed about new mall openings, store launches, and special events happening at your favorite shopping destinations.</li>
</ul>

<h2>Our Commitment</h2>
<p>We are committed to providing accurate, up-to-date information to help you make informed decisions about where to shop. Our team works tirelessly to verify and update our database, ensuring you have access to the latest information about malls in your area.</p>

<h2>Join Our Community</h2>
<p>Become part of the MallFinder community! Whether you're a frequent shopper, a retail enthusiast, or someone looking for their next shopping adventure, we're here to help you discover amazing retail destinations.</p>

<p><em>Happy Shopping!</em></p>
`;

const contactContent = `
<p>We'd love to hear from you! Whether you have questions, suggestions, or want to report an issue, our team is here to help.</p>

<h2>Get in Touch</h2>
<p>There are several ways to reach out to us:</p>

<ul>
  <li><strong>Email:</strong> Contact us at <a href="mailto:info@mallfinder.app">info@mallfinder.app</a> for general inquiries.</li>
  <li><strong>Support:</strong> For technical support or to report incorrect information, email us at <a href="mailto:support@mallfinder.app">support@mallfinder.app</a>.</li>
  <li><strong>Business:</strong> For partnership opportunities or business inquiries, reach out to <a href="mailto:business@mallfinder.app">business@mallfinder.app</a>.</li>
</ul>

<h2>Mailing Address</h2>
<p>MallFinder Headquarters<br />
123 Shopping Center Blvd, Suite 456<br />
Retail City, RC 12345</p>

<h2>Office Hours</h2>
<p>Our team is available during the following hours:</p>
<ul>
  <li>Monday - Friday: 9:00 AM - 6:00 PM</li>
  <li>Saturday: 10:00 AM - 4:00 PM</li>
  <li>Sunday: Closed</li>
</ul>

<h2>Frequently Asked Questions</h2>

<p><strong>Q: How can I add my mall to MallFinder?</strong><br />
A: We're always looking to expand our directory! Please reach out to our business team at business@mallfinder.app with details about your mall.</p>

<p><strong>Q: I found incorrect information. How do I report it?</strong><br />
A: Please email us at support@mallfinder.app with the details about what needs to be corrected. We appreciate your help in keeping our information accurate!</p>

<p><strong>Q: Is MallFinder available as a mobile app?</strong><br />
A: We're currently web-based but are working on mobile applications. Stay tuned for updates!</p>

<h2>Follow Us</h2>
<p>Stay connected and get the latest updates about new malls and shopping trends:</p>
<ul>
  <li>Twitter: <a href="https://twitter.com/mallfinder">@mallfinder</a></li>
  <li>Facebook: <a href="https://facebook.com/mallfinder">MallFinder</a></li>
  <li>Instagram: <a href="https://instagram.com/mallfinder">@mallfinder</a></li>
</ul>

<p>We typically respond to inquiries within 24-48 business hours. Thank you for your patience!</p>
`;

async function main() {
  // Check if pages already exist
  const existingAbout = await prisma.page.findUnique({
    where: { slug: 'about' },
  });

  const existingContact = await prisma.page.findUnique({
    where: { slug: 'contact' },
  });

  if (!existingAbout) {
    await prisma.page.create({
      data: {
        slug: 'about',
        title: 'About Us',
        content: aboutContent,
        metaTitle: 'About Us - MallFinder',
        metaDescription: 'Learn about MallFinder - your premier destination for discovering and exploring the best shopping malls and retail destinations.',
      },
    });
    console.log('Created About Us page');
  } else {
    console.log('About Us page already exists');
  }

  if (!existingContact) {
    await prisma.page.create({
      data: {
        slug: 'contact',
        title: 'Contact Us',
        content: contactContent,
        metaTitle: 'Contact Us - MallFinder',
        metaDescription: 'Get in touch with the MallFinder team. Contact us for questions, support, or business inquiries.',
      },
    });
    console.log('Created Contact Us page');
  } else {
    console.log('Contact Us page already exists');
  }
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
