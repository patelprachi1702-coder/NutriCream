import React from "react";

const items = [
  { name: "Chocolate", price: "₹4" },
  { name: "Vanilla", price: "₹3" },
  { name: "Strawberry", price: "₹4" },
  { name: "Mango", price: "₹5" }
];

function Menu() {
  return (
    <section className="menu" id="menu">
      <h2>Our Flavors</h2>

      <div className="cards">
        {items.map((item, index) => (
          <div className="card" key={index}>
            <h3>{item.name}</h3>
            <p>{item.price}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

export default Menu;