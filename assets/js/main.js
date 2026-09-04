// Image preview before upload
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Show preview image
            const previewImg = document.getElementById('previewImg');
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            
            // Hide placeholder
            document.getElementById('placeholder').style.display = 'none';
            
            // Show analyze button
            document.getElementById('analyzeBtn').style.display = 'inline-block';
        }
        
        reader.readAsDataURL(file);
    }
});