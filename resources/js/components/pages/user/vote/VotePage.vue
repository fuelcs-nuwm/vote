<template>
    <div class="content container-fluid py-3 flex-grow-1 d-flex flex-column">
        <h2>{{ activeEvent && activeEvent.title }}</h2>
        <div class="row flex-grow-1 d-flex">
            <div class="d-flex flex-grow-1 flex-column" :class="[activeVote ? 'col-lg-3' : 'col-lg-5']">
                <p>Медіа:</p>
                <div id="iframe-wrapper" v-html="embedHtml" class=" flex-grow-1" ></div>
                <p>Інформація:</p>
                <div class="info-section flex-grow-1 border border-secondary p-3">
                    Інформаційне вікно
                </div>
            </div>
            <div class="main-section flex-grow-1 d-flex flex-column" :class="[activeVote ? 'col-lg-6' : 'col-lg-4']">
                <p>Голосування:</p>
                <div
                    class="vote-section border border-secondary p-3 mb-3"
                    :class="{'bg-dark' : activeVote}"
                >
                    <div v-if="activeVote">
                        <div class="progress mb-3">
                            <div
                                class="progress-bar" role="progressbar"
                                :style="{width: timerWidth + '%'}"
                                :aria-valuenow="timerWidth"
                                aria-valuemin="0"
                                :aria-valuemax="activeVote.vote_time"></div>
                        </div>
                        <div class="border border-secondary p-3 mb-3 bg-secondary text-white">{{ activeVote.question.title }}</div>
                        <button class="btn btn-success mr-2" @click="saveAnswer(1)">Так</button>
                        <button class="btn btn-danger mr-2" @click="saveAnswer(2)">Ні</button>
                        <button class="btn btn-danger mr-2" @click="saveAnswer(3)">Утримався</button>
                    </div>
                    <div v-if="!activeVote" class="text-center">Немає активного голосування</div>
                </div>
                <div class="question-section">
                    <vuescroll :ops="ops">
                        <div v-if="activeEvent">
                            <p>Запитання:</p>
                            <div v-for="question in questions">
                                <div class="alert alert-secondary">
                                    {{ question.title }}
                                </div>
                                <div v-for="vote in question.votes">
                                    <div class="border border-secondary p-3 mb-3 bg-light">
                                        <div>Результат голосування о {{vote.finished_at}}</div>
                                        <div>Так - {{vote.answer_yes}}</div>
                                        <div>Ні - {{vote.answer_no}}</div>
                                        <div>Утримався - {{vote.answer_abstained}}</div>
                                        <div>Не голосували - {{vote.didnt_vote}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </vuescroll>
                </div>
            </div>
            <div class="col-lg-3 flex-grow-1 d-flex flex-column">
                <p>Чат:</p>
                <Chat></Chat>
            </div>
        </div>
    </div>

</template>

<script>
import {mapMutations} from "vuex";
import vuescroll from "vuescroll";
import Chat from "../../../common/Chat";

export default {
    components: {
        vuescroll,
        Chat
    },
    data() {
        return {
            questions: [],
            activeEvent: null,
            activeVote: null,
            embedHtml: "",
            timerId: null,
            timerCurrentTime: null,
            timerWidth: null,

            ops: {
                vuescroll: {},
                scrollPanel: {},
                rail: {},
                bar: {
                    onlyShowBarOnScroll: true,
                    background: "#ED6E00",
                    //keepShow: true,
                }
            }
        };
    },
    mounted() {
        this.init();

        let channel = Echo.channel('vote')
        channel.listen(".NewVoteEvent", (data) => {
            this.newVote (data.vote)
        });

        channel.listen(".ChangedEventQuestionsEvent", (data) => {
            this.getActiveEvent();
            this.getActiveVote();
        });

        channel.listen(".VoteEndedEvent", (data) => {
            this.getActiveEvent();
            this.getActiveVote();
        });

        channel.listen(".ChangedActiveEvent", (data) => {
            this.getActiveEvent();
        });


    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init () {
            this.getActiveEvent();
            this.getActiveVote();
        },

        startTimer () {
            this.timerCurrentTime = this.activeVote.vote_time;
            this.timerWidth = 100;
            this.timerId = setInterval( () => {
                if (this.timerCurrentTime > 0) {
                    this.timerCurrentTime -= 0.1;
                    this.timerWidth = this.timerCurrentTime * 100 / this.activeVote.vote_time;
                } else {
                    clearInterval (this.timerId);
                }
            }, 100);
        },

        getActiveEvent() {
            this.setIsShowSpinner(true);
            axios
                .get(`/events/active`)
                .then(response => {
                    this.activeEvent = response.data.data;
                    if (this.activeEvent.questions) {
                        this.questions = this.activeEvent.questions;
                    }
                    this.embedHtml = this.activeEvent.embedHtml
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        getActiveVote() {
            this.setIsShowSpinner(true);
            axios
                .get(`/vote/get-active-vote`)
                .then(response => {
                    this.activeVote = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        newVote (vote) {
            this.activeVote = vote;
            this.startTimer ();
        },
        saveAnswer(answer) {
            this.setIsShowSpinner(true);
            axios
                .post(`vote/answers`, {
                    answer: answer
                })
                .then(response => {
                    alert('Голос прийнято.');
                   this.activeVote = null;
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
    }
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);

    .question-section {
        max-height: calc(100vh - 320px);
    }
}

</style>

<style lang="scss">
#iframe-wrapper {
    iframe {
        width: 100%;
        max-height: 300px;
    }
}
</style>
