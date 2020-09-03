var i_pic = 1;
var i_vid = 1;
document.getElementById("addpic").addEventListener("click", function(){
    var pic_columns = document.getElementById("pic_columns")
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Foto ${i_pic}</p>
                <div class="field">
                    <p class="control">
                        <label>Foto: </label>
                        <input type="file" name="pic[]" accept="image/gif, image/jpeg, image/png" multiple="multiple">
                        <br>
                        <label for="pic_description[]">Descripción: </label>
                        <textarea class="textarea" name="pic_description[]" rows="10" cols="30"></textarea>
                    </p>
                </div>
            </div>
        </div>
    </div>
    `;
    pic_columns.insertAdjacentHTML('beforeend', html)
    i_pic++;
})

document.getElementById("addvid").addEventListener("click", function(){
    var vid_columns = document.getElementById("vid_columns")
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Vídeo ${i_vid}</p>
                <div class="field">
                    <p class="control">
                        <label>Foto: </label>
                        <input type="file" name="vid[]" accept="video/mp4, video/webm" multiple="multiple">
                        <br>
                        <label for="vid_description[]">Descripción: </label>
                        <textarea class="textarea" name="vid_description[]" rows="10" cols="30"></textarea>
                    </p>
                </div>
            </div>
        </div>
    </div>
    `;
    vid_columns.insertAdjacentHTML('beforeend', html)
    i_vid++;
})
