const imageNames = [
    "002.png",
    "003.png",
    "005.png",
    "006.png",
    "007.png",
    "008.png",
    "009.png",
    "010.png",
    "012.png",
    "023.png",
    "024.png",
    "030.png",
    "032.png",
    "lick.png",
    "read.png",
    "sleep.png",
    "tired.png",
];

const background = document.querySelector(".background");
const tile = document.createElement("canvas");
const context = tile.getContext("2d");
const base = document.createElement("canvas");
const baseContext = base.getContext("2d");
let composition;

tile.className = "background__tile";
background.append(tile);

function loadImage(name) {
    return new Promise((resolve, reject) => {
        const image = new Image();

        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error(`Nepodarilo sa načítať images/${name}`));
        image.src = `images/${name}`;
    });
}

async function createImageTile() {
    const images = await Promise.all(imageNames.map(loadImage));

    if (!composition) {
        const placed = [];
        const minDistance = 0.16;

        composition = [...images]
            .sort(() => Math.random() - 0.5)
            .map((image) => {
                let position = {
                    x: Math.random(),
                    y: Math.random(),
                };

                for (let attempt = 0; attempt < 100; attempt++) {
                    const candidate = {
                        x: Math.random(),
                        y: Math.random(),
                    };
                    const hasSpace = placed.every((other) => {
                        const horizontalDistance = Math.min(
                            Math.abs(candidate.x - other.x),
                            1 - Math.abs(candidate.x - other.x)
                        );
                        const verticalDistance = Math.min(
                            Math.abs(candidate.y - other.y),
                            1 - Math.abs(candidate.y - other.y)
                        );

                        return Math.hypot(
                            horizontalDistance,
                            verticalDistance
                        ) >= minDistance;
                    });

                    position = candidate;
                    if (hasSpace) break;
                }

                placed.push(position);

                return {
                    image,
                    xOffset: position.x,
                    yOffset: position.y,
                    size: 58 + Math.random() * 54,
                };
            });
    }

    const width = window.innerWidth;
    const height = window.innerHeight;

    tile.width = width * 2;
    tile.height = height * 2;
    base.width = width;
    base.height = height;
    baseContext.clearRect(0, 0, width, height);

    function drawWrapped(image, x, y, size) {
        for (const offsetX of [-width, 0, width]) {
            for (const offsetY of [-height, 0, height]) {
                baseContext.drawImage(
                    image,
                    x + offsetX,
                    y + offsetY,
                    size,
                    size
                );
            }
        }
    }

    composition.forEach(({ image, size, xOffset, yOffset }) => {
        const x = xOffset * width - size / 2;
        const y = yOffset * height - size / 2;

        baseContext.globalAlpha = 0.82;
        drawWrapped(image, x, y, size);
    });

    context.clearRect(0, 0, tile.width, tile.height);
    context.drawImage(base, 0, 0);
    context.drawImage(base, width, 0);
    context.drawImage(base, 0, height);
    context.drawImage(base, width, height);
}

createImageTile().catch((error) => {
    console.error(error);
});

window.addEventListener("resize", () => {
    createImageTile().catch((error) => {
        console.error(error);
    });
});
