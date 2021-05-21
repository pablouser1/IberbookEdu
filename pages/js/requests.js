const requests = async (url, type, data = null) => {
    const res = await fetch(url, {
        method: type,
        body: data
    })
    if (res.ok) {
        const resJson = await res.json()
        return resJson
    }
    return false
}
