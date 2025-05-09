<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCycle - Add Educational Book</title>
    <link rel="stylesheet" href="../CSS/addBook.css">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navigation-bar">
    
    <div class="navdiv">
     <div class="logo">EduCycle</div>
 
      <div class="link">
      
       <a href="userAccount.php" class="nav-link">Account</a>
      </div>
     </div>
 </nav>
    </header>

    <main class="container">
        <div class="add-book-container">
            <div class="page-header">
                <h1>Add Educational Book</h1>
                <a href="users.php" class="back-link">← Back to Exchange</a>
            </div>
            
            <form id="add-book-form" action="../Database/listBook.php" method="POST" class="add-book-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="book-title">Book Title*</label>
                    <input type="text" id="book-title" name="book-title" required placeholder="Enter the full title of the book">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="book-author">Author*</label>
                        <input type="text" id="book-author" name="book-author" required placeholder="Author's full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="book-year">Publication Year</label>
                        <input type="number" id="book-year" name="year" placeholder="e.g., 2022" min="1900" max="2025">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="book-category">Category*</label>
                        <select id="book-category" name="category" required>
                            <option value="" disabled selected>Select a category</option>
                            <option value="law">Law & Governance</option>
                            <option value="science">Sciences</option>
                            <option value="math">Mathematics</option>
                            <option value="engineering">Engineering</option>
                            <option value="medicine">Medicine & Health</option>
                            <option value="business">Business & Economics</option>
                            <option value="humanities">Humanities</option>
                            <option value="computer">Computer Science</option>
                            <option value="education">Education</option>
                            <option value="social">Social Sciences</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="book-condition">Condition*</label>
                        <select id="book-condition" name="book-condition" required>
                            <option value="" disabled selected>Select condition</option>
                            <option value="like-new">Like New</option>
                            <option value="very-good">Very Good</option>
                            <option value="good">Good</option>
                            <option value="acceptable">Acceptable</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="book-edition">Edition</label>
                    <input type="text" id="book-edition" name="book-edition" placeholder="e.g., 3rd Edition">
                </div>
                
                <div class="form-group">
                    <label for="book-description">Description</label>
                    <textarea id="book-description" name="book-description" rows="4" placeholder="Provide details about the book, any highlights, notes, or special features"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="book-cover">Book Cover Image</label>
                    <div class="file-upload">
                        <input type="file" id="book-cover" name="book-cover" accept="image/*">
                        <div class="file-upload-placeholder">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                            <p>Click to upload or drag and drop</p>
                            <span>SVG, PNG, JPG or GIF (max. 2MB)</span>
                        </div>
                        <div class="file-preview" id="file-preview"></div>
                    </div>
                </div>
                
                <div class="form-divider"></div>
                
                <div class="form-group">
                    <label for="contact-method">Preferred Contact Method*</label>
                    <select id="contact-method" name="contact-method" required>
                        <option value="" disabled selected>Select contact method</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="in-app">In-app Messaging</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="contact-info">Contact Information*</label>
                    <input type="text" id="contact-info" name="contact-info" required placeholder="Email address or phone number">
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" required>
                        <span>I confirm this is an educational book and all information is accurate*</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="window.location.href='userAccount.php'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 EduCycle</p>
            <div class="footer-links">
               
            </div>
        </div>
    </footer>
    
    <script>
        // Simple file preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('book-cover');
            const filePreview = document.getElementById('file-preview');
            const filePlaceholder = document.querySelector('.file-upload-placeholder');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        filePreview.innerHTML = `<img src="${e.target.result}" alt="Book cover preview">`;
                        filePreview.style.display = 'block';
                        filePlaceholder.style.display = 'none';
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // Remove the custom form submission logic to allow normal submission
        document.getElementById('add-book-form').addEventListener('submit', function(event) {
            // Allow the form to submit normally to the PHP script
        });
    </script>
</body>
</html>