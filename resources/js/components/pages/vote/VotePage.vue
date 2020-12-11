<template>
    <div class="content container-fluid py-3 flex-grow-1 d-flex flex-column">
        <h2>{{ activeEvent.title }}</h2>
        <div class="row flex-grow-1 d-flex">
            <div class="col-lg-5 d-flex flex-grow-1 flex-column">
                <p>Медіа:</p>
                <div id="iframe-wrapper" v-html="embedHtml" class=" flex-grow-1" ></div>
                <p>Інформація:</p>
                <div class="info-section flex-grow-1 border border-secondary p-3">
                    Інформаційне вікно
                </div>
            </div>
            <div class="col-lg-4 main-section">
                <vuescroll :ops="ops">
                    <div v-if="activeEvent">
                        <p>Голосування:</p>
                        <div class="vote-section flex-grow-1 border border-secondary p-3 mb-3">
                            Вікно голосуваня
                        </div>
                        <p>Запитання:</p>

                        <div class="alert alert-secondary" v-for="question in questions">
                            {{ question.title }}
                        </div>
                    </div>
                </vuescroll>
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

export default {
    data() {
        return {
            questions: [],
            activeEvent: null,
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
    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init () {
            this.getActiveEvent()
        },

        getActiveEvent() {
            this.setIsShowSpinner(true);
            axios
                .get(`/events/active`)
                .then(response => {
                    console.log(response.data);
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
    }
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);

    .main-section {
        height: calc(100vh - 200px);
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
