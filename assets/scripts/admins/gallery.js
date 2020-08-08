var i_gallery = 1;
document.getElementById("addpic").addEventListener("click", function(){
    var gallery_columns = document.getElementById("gallery_columns")
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Foto ${i_gallery}</p>
                <div class="field">
                    <p class="control">
                        <label>Foto: </label>
                        <input type="file" name="gallery[]" accept="image/*" multiple="multiple">
                        <br>
                        <label for="gallery_description[]">Descripción: </label>
                        <textarea class="textarea" name="gallery_description[]" rows="10" cols="30"></textarea>
                    </p>
                </div>
            </div>
        </div>
    </div>
    `;
    gallery_columns.insertAdjacentHTML('beforeend', html)
    i_gallery++;
})