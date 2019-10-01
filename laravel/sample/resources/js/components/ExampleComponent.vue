<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ title }}
                        <span class="badge-success">{{ category_name }}</span>
                    </div>

                    <div class="card-body text-center drill-body">
                        <button class="btn btn-primary" v-on:click="doDrill" v-if="!isStarted">START</button>
                        <p v-if="isCountDown" style="font-size: 100px;">{{countDownNum}}</p>
                        <template v-if="isStarted && !isCountDown && !isEnd">
                            <p>{{timerNum}}</p>
                            <span v-for="(word, index) in problemWords" :key="index" :class="{'text-primary': index < currentWordNum}">{{word}}</span>
                        </template>

                        <template v-if="isEnd">
                            <p>あなたのスコア</p>
                            <p>{{typingScore}}</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import keyCodeMap from '../master/keymap'
    export default {
        //  コンポーネント呼び出し側が設定するデータリスト
        props: ['title', 'drill', 'category_name'],
        // 初期化変数
        data: function() {
            return {
                countDownNum: 3,
                timerNum: 30,
                missNum: 0,
                wpm: 0,
                isStarted: false,
                isEnd: false,
                isCountDown: false,
                currentWordNum: 0,
                currentProblemNum: 0,
            }
        },

        // プロパティー名
        computed: {
            problemText: function() {
                return this.drill['problem' + this.currentProblemNum]
            },
            problemWords: function() {
                return Array.from(this.drill['problem' + this.currentProblemNum])
            },
            problemKeyCodes: function() {
                if (!Array.from(this.drill['problem' + this.currentProblemNum]).length) {
                    return null
                }

                let problemKeyCodes = [] 
                console.log(Array.from(this.drill['problem' + this.currentProblemNum]));
                Array.from(this.drill['problem' + this.currentProblemNum]).forEach((text) => {
                    $.each(keyCodeMap, (keyText, keyCode) => {
                        if (text === keyText) {
                            problemKeyCodes.push(keyCode);
                        }
                    })
                })

                console.log(problemKeyCodes)

                return problemKeyCodes
            },
            totalWordNum: function() {
                return this.problemKeyCodes.length;
            },
            typingScore: function() {
                return (this.wpm * 2) * (1 - this.missNum / (this.wpm * 2))
            }
        },

        // メソッド定義一覧
        methods: {
            doDrill: function() {
                this.isStarted = true
                this.countDown()
            },

            countDown: function() {
                const countSound = new Audio('../sounds/Countdown01-5.mp3')
                const startSound = new Audio('../sounds/Countdown01-6.mp3')

                this.isCountDown = true

                this.soundPlay(countSound)

                let timer = window.setInterval(() => {
                    this.countDownNum -= 1

                    if (this.countDownNum <= 0) {
                        this.isCountDown = false

                        this.soundPlay(startSound)

                        window.clearInterval(timer)
                        this.countTimer()
                        this.showFirstProblem()

                        return
                    }

                    this.soundPlay(countSound)
                }, 1000)
            },

            showFirstProblem: function() {
                const okSound = new Audio('../sounds/punch-middle.mp3')
                const ngSound = new Audio('../sounds/sword-clash4.mp3')
                const nextSound = new Audio('../sounds/punch-high2.mp3')

                $(window).on('keypress', e => {
                    console.log(e.which)
                    if (e.which === this.problemKeyCodes[this.currentWordNum]) {
                        console.log('正解！！')

                        this.soundPlay(okSound)

                        ++this.currentWordNum 
                        ++this.wpm
                        console.log('現在回答の文字数目:' + this.currentWordNum)

                        if (this.totalWordNum === this.currentWordNum) {
                            ++this.currentProblemNum
                            this.currentWordNum = 0

                            if (this.problemText === null) {
                                this.isEnd = true
                            }
                            else {
                                console.log('次の段階へ！！')
                                this.soundPlay(nextSound)
                            }
                        }
                    }
                    else {
                        console.log('不正解')

                        this.soundPlay(ngSound)
                        ++this.missNum

                        console.log('現在回答の文字数目:' + this.currentWordNum)
                    }
                })
            },

            countTimer: function() {
                const endSound = new Audio('../sounds/gong-played2.mp3')

                let timer = window.setInterval(() => {
                    this.timerNum -= 1

                    if (this.timerNum <= 0) {
                        this.isEnd = true

                        window.clearInterval(timer)
                        this.soundPlay(this.endSound)
                    }
                }, 1000)
            },

            soundPlay: function(audio) {
                audio.currentTime = 0
                audio.play()
            }
        }
    }
</script>
