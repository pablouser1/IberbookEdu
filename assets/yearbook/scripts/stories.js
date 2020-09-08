// -- TODO, limpiar y adaptar a Vue -- //

var stories_teachers = [];
var stories_students = [];
// Common zuck.js settings
const story_settings = {
    skin: 'snapgram',
    avatars: true,
    list: false,
    openEffect: true,
    cubeEffect: true,
    autoFullScreen: true,
    backButton: true,
    backNative: false,
    previousTap: true,
    localStorage: true,
    reactive: false
}

// Students stories
students_js.forEach(student => {
    stories_students.push({
        id: student.userid,
        photo: student.photo,
        name: student.abbr,
        items: [
            {
                id: student.userid,
                type: "video",
                src: student.video,
                link: student.url,
                time: student.zuckdate
            }
        ]
    })
})

// Teachers stories
teachers_js.forEach(teacher => {
    stories_teachers.push({
        id: teacher.userid,
        photo: teacher.photo,
        name: teacher.abbr,
        items: [
            {
                id: teacher.userid,
                type:"video",
                src: teacher.video,
                link: teacher.url,
                time: teacher.zuckdate
            }
        ]
    })
})

// Teachers
let steachers = new Zuck('stories_teachers', {
    story_settings,
    stories: stories_teachers,
});

// Students
let sstudents = new Zuck('stories_students', {
    story_settings,
    stories: stories_students,
});

