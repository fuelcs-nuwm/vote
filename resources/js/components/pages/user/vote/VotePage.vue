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
            <div class="main-section" :class="[activeVote ? 'col-lg-6' : 'col-lg-4']">
                <p>Голосування:</p>
                <div
                    class="vote-section flex-grow-1 border border-secondary p-3 mb-3"
                    :class="{'bg-dark' : activeVote}"
                >
                    <div v-if="activeVote">
                        <div class="border border-secondary p-3 mb-3 bg-secondary text-white">{{ activeVote.question.title }}</div>
                        <button class="btn btn-success mr-2">Так</button>
                        <button class="btn btn-danger mr-2">Ні</button>
                        <button class="btn btn-danger mr-2">Утримався</button>
                    </div>
                </div>
                <div class="question-section">
                    <vuescroll :ops="ops">
                        <div v-if="activeEvent">
                            <p>Запитання:</p>

                            <div class="alert alert-secondary" v-for="question in questions">
                                {{ question.title }}
                            </div>
                        </div>
                    </vuescroll>
                </div>
            </div>
            <div class="col-lg-3 flex-grow-1 d-flex flex-column">
                <p>Чат:</p>
                <div class="chat flex-grow-1 border border-secondary p-3">
                    Вікно чату
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import {mapMutations} from "vuex";
import vuescroll from "vuescroll";

export default {
    components: {
        vuescroll
    },
    data() {
        return {
            questions: [],
            activeEvent: null,
            activeVote: null,
            embedHtml: "",

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
            console.log(JSON. parse(data.vote))
            this.newVote (JSON. parse(data.vote))
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
        }
    }
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);

    .question-section {
        height: calc(100vh - 320px);
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
