var i_teachers = 2;
function addtocolumn(){
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Profesor ${i_teachers}</p>
                <div class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="text" name="teachers_names[]" placeholder="Nombre">
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control">
                        <input class="input" type="text" name="teachers_surnames[]" placeholder="Apellidos">
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="text" name="teachers_subjects[]" placeholder="Asignatura">
                        <span class="icon is-small is-left">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control">
                        <label>Foto: </label>
                        <input type="file" name="pic_teacher[]" accept="image/*" multiple="multiple">
                    </p>
                </div>
                <div class="field">
                    <p class="control">
                        <label>Vídeo: </label>
                        <input type="file" name="vid_teacher[]" accept="video/*" multiple="multiple">
                    </p>
                </div>
            </div>
        </div>
    </div>`;
    var column = document.getElementById("teachers_columns")
    var amount = document.getElementById("teachers_amount")
    i_teachers++;
    amount.value = Number(amount.value) + 1;
    column.innerHTML += html;
}