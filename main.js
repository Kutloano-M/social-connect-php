// js/main.js

// This script adds interactivity to the web app:
// Shows a preview when uploading a post image.
// Adds simple, live search suggestions as the user types.

document.addEventListener('DOMContentLoaded', function() {

  //IMAGE PREVIEW WHEN CREATING A POST
  const postImage = document.getElementById('postImage'); // The file input field
  const preview = document.getElementById('postPreview'); // The image preview element

  // If the image upload field exists on the page
  if (postImage) {
    postImage.addEventListener('change', function(e) {
      const file = e.target.files[0]; // Get the selected file
      if (!file) return; // Exit if no file is selected

      // Read the selected image file and display it as a preview
      const reader = new FileReader();
      reader.onload = function(ev) {
        preview.src = ev.target.result; // Set preview image source
        preview.style.display = 'block'; // Make the preview visible
      };
      reader.readAsDataURL(file); // Convert file to a Base64 data URL
    });
  }


  // SIMPLE LIVE SEARCH (TYPEAHEAD)
  const searchInput = document.getElementById('search');
  
  if (searchInput) {
    let timeout = null; // Used to delay the search until user stops typing

    searchInput.addEventListener('input', function() {
      clearTimeout(timeout); // Cancel previous search timer
      const q = this.value.trim(); // Get search query text
      if (q.length < 2) return; // Ignore very short searches

      // Delay fetching results by 300ms (debouncing)
      timeout = setTimeout(function() {
        // Use Fetch API to load search results from search.php
        fetch('search.php?q=' + encodeURIComponent(q))
          .then(res => res.text())
          .then(html => {
            // Create a temporary container to parse returned HTML
            const tmp = document.createElement('div');
            tmp.innerHTML = html;

            // Extract the list of results from the HTML
            const list = tmp.querySelector('.search-results');

            // Remove old suggestions if any exist
            const existing = document.querySelector('.live-suggestions');
            if (existing) existing.remove();

            // If there are results, show them below the search box
            if (list) {
              const suggestions = document.createElement('div');
              suggestions.className = 'live-suggestions';
              suggestions.innerHTML = list.innerHTML;
              document.body.appendChild(suggestions);

              // Position the suggestions under the search box
              const rect = searchInput.getBoundingClientRect();
              suggestions.style.position = 'absolute';
              suggestions.style.left = rect.left + 'px';
              suggestions.style.top = (rect.bottom + window.scrollY) + 'px';
              suggestions.style.zIndex = 1000;
            }
          })
          .catch(err => console.error('Search error:', err));
      }, 300);
    });

    // Close the suggestion box when user clicks elsewhere
    document.addEventListener('click', function(e) {
      const suggestionBox = document.querySelector('.live-suggestions');
      if (suggestionBox && !searchInput.contains(e.target)) {
        suggestionBox.remove();
      }
    });
  }

});
