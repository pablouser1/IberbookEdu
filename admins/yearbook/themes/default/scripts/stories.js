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
    reactive: true
}

// Return array with story info
function setupStories(type) {
    let group = {}
    let stories = []
    switch (type) {
        case "students":
            group = students_js
            break;
        case "teachers":
            group = teachers_js
            break;
        default:
            console.error(`Error loading stories ${type}, this group does not exist`)
            break;
    }
    group.forEach(person => {
        stories.push({
            id: person.userid,
            photo: person.photo,
            name: person.abbr,
            items: [
                {
                    id: person.userid,
                    type: "video",
                    src: person.video,
                    link: person.url,
                    time: person.zuckdate
                }
            ]
        })
    })
    return stories
}
