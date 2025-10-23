const chokidar = require('chokidar');

const paths = JSON.parse(process.argv[2]);
const ignored = JSON.parse(process.argv[3]);
const extensions = JSON.parse(process.argv[4]);


function checkExtension(path, stats, extensions) {
    return stats?.isFile() && !endsWithAny(extensions, path);
}
function endsWithAny(suffixes, string) {
    for (let suffix of suffixes) {
        if(string.endsWith(suffix))
            return true;
    }
    return false;
}

const watcher = chokidar.watch(paths, {
    ignored: (path, stats) => checkExtension(path, stats, extensions) || ignored.includes(path),
    ignoreInitial: true,
    awaitWriteFinish: {
        stabilityThreshold: 2000,
        pollInterval: 100
    }
});

watcher.on('all', (event, path) => {
    console.log(event, path);
});
