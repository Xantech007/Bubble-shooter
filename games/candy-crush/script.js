document.addEventListener("DOMContentLoaded", () => {
    candyCrushGame();
});

function candyCrushGame() {
    // DOM Elements
    const grid = document.querySelector(".grid");
    const scoreDisplay = document.getElementById("score");
    const timerDisplay = document.getElementById("timer");
    const modeSelection = document.getElementById("modeSelection");
    const endlessButton = document.getElementById("endlessMode");
    const timedButton = document.getElementById("timedMode");
    const changeModeButton = document.getElementById("changeMode");

    // Game Variables
    const width = 8;
    const squares = [];
    let score = 0;
    let currentMode = null;
    let timeLeft = 120;
    let gameInterval = null;
    let timerInterval = null;

    const candyColors = [
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/red-candy.png)",
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/blue-candy.png)",
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/green-candy.png)",
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/yellow-candy.png)",
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/orange-candy.png)",
        "ur[](https://raw.githubusercontent.com/arpit456jain/Amazing-Js-Projects/master/Candy%20Crush/utils/purple-candy.png)",
    ];

    // Create Board
    function createBoard() {
        grid.innerHTML = "";
        squares.length = 0;

        for (let i = 0; i < width * width; i++) {
            const square = document.createElement("div");
            square.setAttribute("id", i);
            let randomColor = Math.floor(Math.random() * candyColors.length);
            square.style.backgroundImage = candyColors[randomColor];
            square.style.backgroundSize = "85%";
            square.style.backgroundRepeat = "no-repeat";
            square.style.backgroundPosition = "center";

            // Desktop drag support
            square.setAttribute("draggable", true);
            square.addEventListener("dragstart", dragStart);
            square.addEventListener("dragover", dragOver);
            square.addEventListener("drop", dragDrop);
            square.addEventListener("dragend", dragEnd);

            // Mobile touch support
            square.addEventListener("touchstart", touchStart, { passive: false });
            square.addEventListener("touchmove", touchMove, { passive: false });
            square.addEventListener("touchend", touchEnd);

            grid.appendChild(square);
            squares.push(square);
        }
    }

    // ====================== DRAG & DROP (Desktop) ======================
    let draggedId = null;
    let replacedId = null;

    function dragStart(e) {
        draggedId = parseInt(this.id);
    }

    function dragOver(e) {
        e.preventDefault();
    }

    function dragDrop(e) {
        e.preventDefault();
        replacedId = parseInt(this.id);
        if (isValidMove(draggedId, replacedId)) {
            swapCandies(draggedId, replacedId);
        }
    }

    function dragEnd() {
        draggedId = null;
        replacedId = null;
    }

    // ====================== TOUCH SUPPORT (Mobile) ======================
    let touchStartId = null;

    function touchStart(e) {
        e.preventDefault();
        touchStartId = parseInt(this.id);
        this.style.transform = "scale(1.15)";
    }

    function touchMove(e) {
        e.preventDefault();
        if (!touchStartId) return;

        const touch = e.touches[0];
        const element = document.elementFromPoint(touch.clientX, touch.clientY);

        if (element && element.id !== touchStartId.toString()) {
            const targetId = parseInt(element.id);
            if (isValidMove(touchStartId, targetId)) {
                swapCandies(touchStartId, targetId);
                touchStartId = null;
            }
        }
    }

    function touchEnd() {
        if (touchStartId !== null) {
            const square = squares[touchStartId];
            if (square) square.style.transform = "scale(1)";
            touchStartId = null;
        }
    }

    // Helper: Valid adjacent move?
    function isValidMove(id1, id2) {
        const diff = Math.abs(id1 - id2);
        return diff === 1 || diff === width;
    }

    // Swap two candies
    function swapCandies(id1, id2) {
        const temp = squares[id1].style.backgroundImage;
        squares[id1].style.backgroundImage = squares[id2].style.backgroundImage;
        squares[id2].style.backgroundImage = temp;

        // Check if swap created a match
        setTimeout(() => {
            if (!checkForMatches()) {
                // No match → revert swap
                const temp2 = squares[id1].style.backgroundImage;
                squares[id1].style.backgroundImage = squares[id2].style.backgroundImage;
                squares[id2].style.backgroundImage = temp2;
            }
        }, 80);
    }

    // ====================== MATCH CHECKING ======================
    function checkForMatches() {
        let matchFound = false;

        // Check rows and columns for 3 and 4
        for (let i = 0; i < width * width; i++) {
            // Row of 3
            if (i % width < width - 2) {
                const rowOfThree = [i, i + 1, i + 2];
                if (isMatch(rowOfThree)) {
                    removeCandies(rowOfThree);
                    matchFound = true;
                }
            }

            // Row of 4
            if (i % width < width - 3) {
                const rowOfFour = [i, i + 1, i + 2, i + 3];
                if (isMatch(rowOfFour)) {
                    removeCandies(rowOfFour);
                    matchFound = true;
                }
            }

            // Column of 3
            if (i < width * (width - 2)) {
                const colOfThree = [i, i + width, i + 2 * width];
                if (isMatch(colOfThree)) {
                    removeCandies(colOfThree);
                    matchFound = true;
                }
            }

            // Column of 4
            if (i < width * (width - 3)) {
                const colOfFour = [i, i + width, i + 2 * width, i + 3 * width];
                if (isMatch(colOfFour)) {
                    removeCandies(colOfFour);
                    matchFound = true;
                }
            }
        }

        if (matchFound) {
            scoreDisplay.textContent = score;
            setTimeout(() => {
                moveIntoSquareBelow();
                setTimeout(checkForMatches, 150); // Chain reaction
            }, 200);
        }
        return matchFound;
    }

    function isMatch(arr) {
        const color = squares[arr[0]].style.backgroundImage;
        if (!color) return false;
        return arr.every(index => squares[index].style.backgroundImage === color);
    }

    function removeCandies(arr) {
        const points = arr.length * 10;
        score += points;

        arr.forEach(index => {
            const sq = squares[index];
            if (sq) {
                sq.style.transition = "transform 0.2s";
                sq.style.transform = "scale(0.1)";
                setTimeout(() => {
                    sq.style.backgroundImage = "";
                    sq.style.transform = "scale(1)";
                }, 180);
            }
        });
    }

    // Gravity + refill
    function moveIntoSquareBelow() {
        for (let i = 0; i < width * (width - 1); i++) {
            if (squares[i + width].style.backgroundImage === "") {
                squares[i + width].style.backgroundImage = squares[i].style.backgroundImage;
                squares[i].style.backgroundImage = "";
            }
        }

        // Refill top row
        for (let i = 0; i < width; i++) {
            if (squares[i].style.backgroundImage === "") {
                const randomColor = Math.floor(Math.random() * candyColors.length);
                squares[i].style.backgroundImage = candyColors[randomColor];
            }
        }
    }

    // Game Loop
    function gameLoop() {
        checkForMatches();
    }

    // Start Game
    function startGame(mode) {
        currentMode = mode;
        modeSelection.style.display = "none";
        document.querySelector(".game-container").style.display = "flex";

        createBoard();
        score = 0;
        scoreDisplay.textContent = "0";

        gameInterval = setInterval(gameLoop, 120);

        if (mode === "timed") {
            timeLeft = 120;
            updateTimerDisplay();
            timerInterval = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                if (timeLeft <= 0) endGame();
            }, 1000);
        } else {
            timerDisplay.textContent = "∞";
        }
    }

    function updateTimerDisplay() {
        if (currentMode === "timed") {
            const min = Math.floor(timeLeft / 60);
            const sec = timeLeft % 60;
            timerDisplay.textContent = `${min}:${sec < 10 ? '0' : ''}${sec}`;
        }
    }

    function endGame() {
        clearInterval(gameInterval);
        clearInterval(timerInterval);
        alert(`Time's Up!\n\nYour final score: ${score}`);
        changeMode();
    }

    function changeMode() {
        clearInterval(gameInterval);
        if (timerInterval) clearInterval(timerInterval);

        document.querySelector(".game-container").style.display = "none";
        modeSelection.style.display = "flex";
    }

    // Event Listeners
    endlessButton.addEventListener("click", () => startGame("endless"));
    timedButton.addEventListener("click", () => startGame("timed"));
    changeModeButton.addEventListener("click", changeMode);
}
