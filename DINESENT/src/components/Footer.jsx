import React from "react";
import "../Style/Footer.css";

const Footer = () => {
  return (
    <footer className="footer">
      <div className="footer-container fluid">
        {/* Column 1: Logo & Text */}
        <div className="footer-column">
          <div className="footer-logo">WIX STUDIO</div>
          <p className="footer-description">
            Wix Studio is the platform built for agencies and enterprises. Smart
            design capabilities, flexible dev tools and streamlined business
            management mean you can do more—with more.
          </p>
          <div className="footer-icons">
            <span>🌐</span>
            <span>📘</span>
            <span>📸</span>
            <span>🐦</span>
            <span>▶️</span>
          </div>
        </div>

        {/* Column 2: Product */}
        <div className="footer-column">
          <h3 className="footer-heading">PRODUCT</h3>
          <ul>
            {[
              "Design",
              "Development",
              "Enterprise",
              "All Features",
              "Business Solutions",
              "Commerce",
              "CMS",
              "Management Tools",
              "SEO",
              "Marketing Integrations",
              "Security",
              "Reliability & Performance",
              "Top Features",
            ].map((item) => (
              <li key={item}>{item}</li>
            ))}
          </ul>
        </div>

        {/* Column 3: Resources */}
        <div className="footer-column">
          <h3 className="footer-heading">RESOURCES</h3>
          <ul>
            {[
              "Wix Studio Academy",
              "Community",
              "Forum",
              "Inspiration",
              "Blog",
              "Partner Program",
              "Help Center",
              "Pricing",
              "Brand Guidelines",
            ].map((item) => (
              <li key={item}>{item}</li>
            ))}
          </ul>
        </div>

        {/* Column 4: More from Wix */}
        <div className="footer-column">
          <h3 className="footer-heading">MORE FROM WIX</h3>
          <ul>
            {[
              "Website Builder",
              "Website Design",
              "Website Templates",
              "eCommerce Website",
              "Appointment Scheduling",
              "Portfolio Website",
              "Blog Website",
            ].map((item) => (
              <li key={item}>{item}</li>
            ))}
          </ul>
        </div>

        {/* Column 5: Company */}
        <div className="footer-column">
          <h3 className="footer-heading">COMPANY</h3>
          <ul>
            {[
              "About Wix",
              "Contact Us",
              "Press & Media",
              "Accessibility Statement",
              "Site Map",
              "Careers",
            ].map((item) => (
              <li key={item}>{item}</li>
            ))}
          </ul>
        </div>
      </div>

      <div className="footer-bottom">
        <div className="footer-links">
          <a href="#">Terms of Use</a>
          <a href="#">Privacy Policy</a>
        </div>
        <p className="footer-copy">
          Wix Studio is part of Wix.com Ltd. © 2006–2024
        </p>
      </div>
    </footer>
  );
};

export default Footer;
