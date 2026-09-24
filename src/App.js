import React from "react";
import "./App.css";

function App() {
  const flavors = [
    { name: "Vanilla", price: "₹80" },
    { name: "Chocolate", price: "₹100" },
    { name: "Strawberry", price: "₹90" },
    { name: "Mango", price: "₹110" }
  ];

  return (
    <div>
      {/* Navbar */}
      <nav className="navbar">
        <h2>🍦 Ice Cream Shop</h2>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#menu">Menu</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </nav>

      {/* Hero Section */}
      <section className="hero" id="home">
        <h1>Welcome to Ice Cream Heaven</h1>
        <p>Fresh • Creamy • Delicious</p>
        <button>Order Now</button>
      </section>

      {/* Menu Section */}
      <section className="menu" id="menu">
        <h2>Our Flavors</h2>
        <div className="cards">
          {flavors.map((item, index) => (
            <div className="card" key={index}>
              <h3>{item.name}</h3>
              <p>{item.price}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Contact Section */}
      <section className="contact" id="contact">
        <h2>Contact Us</h2>
        <form>
          <input type="text" placeholder="Your Name" />
          <input type="email" placeholder="Your Email" />
          <textarea placeholder="Message"></textarea>
          <button type="submit">Send</button>
        </form>
      </section>

      {/* Footer */}
      <footer>
        <p>© 2026 Ice Cream Shop. All Rights Reserved.</p>
      </footer>
    </div>
  );
}

export default App;
