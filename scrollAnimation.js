// scrollAnimation.js
// This file handles smooth scrolling for anchor links and triggers animations when elements scroll into view.

document.addEventListener("DOMContentLoaded", function() {
  // Smooth scrolling for all anchor links with href starting with "#"
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach(link => {
    link.addEventListener("click", function(e) {
      e.preventDefault();
      const targetID = this.getAttribute("href");
      const targetElement = document.querySelector(targetID);
      if (targetElement) {
        targetElement.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  // Scroll-triggered animations for elements with the class "animate-on-scroll"
  // Customize the threshold value as needed.
  const observerOptions = {
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("scrolled");
        // Optionally, stop observing the element after it has been animated.
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Select all elements that should animate on scroll
  const animatedElements = document.querySelectorAll(".animate-on-scroll");
  animatedElements.forEach(element => observer.observe(element));
});
