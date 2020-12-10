<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Запитання</h2>
        <p class="breadcrumb">
            <router-link tag="span" :to="{ name: 'admin.events'}">
                <b class="breadcrumb-item link">Налаштування подій</b>
            </router-link>
            <span>&nbsp;- Запитання</span></p>
        <h5>Подія: {{ event && event.title }}</h5>

        <form @submit.prevent="storeQuestion">
            <div class="input-group mb-3">

                <textarea
                    type="text"
                    class="form-control"
                    placeholder="Нове запитання"
                    v-model="newQuestion.title"
                    rows="3"
                ></textarea>
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeQuestion">Додати запитання</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newQuestion.title.required && $v.newQuestion.title.$dirty "
            class="text-danger"
        >Введіть назву
        </div>

        <hr>

        <div v-for="question in questions">
            <div v-if="editId == -1" class="input-group mb-3">
                <textarea
                    type="text" class="form-control"
                    placeholder="Нова подія"
                    v-model="question.title"
                    rows="3"
                    @click="editQuestion(question)"
                    readonly
                ></textarea>
                <div class="input-group-append">
                    <span class="input-group-text" @click="deleteQuestion(question.id)">Видалити запитання</span>
                </div>
            </div>
            <div v-if="editId == question.id" class="input-group mb-3">
                <textarea
                    type="text"
                    class="form-control"
                    placeholder="Нове запитання"
                    v-model="editedQuestion.title"
                    rows="3"
                ></textarea>
                <div class="input-group-append">
                    <span class="input-group-text" @click="updateQuestion">Редагувати</span>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                </div>
            </div>
            <div
                v-if="editId == question.id && !$v.editedQuestion.title.required && $v.editedQuestion.title.$dirty"
                class="text-danger"
            >Введіть назву
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from "vuex";
import {clone as _clone} from 'lodash';
import {
    required,
    maxLength
} from "vuelidate/lib/validators";

export default {
    data() {
        return {
            eventId: null,
            event: null,
            editId: -1,
            questions: [],
            newQuestion: {
                title: ""
            },
            editedQuestion: {
                title: ""
            }
        };
    },
    validations: {
        newQuestion: {
            title: {
                required,
                maxLength: maxLength(5000),
            },
        },
        editedQuestion: {
            title: {
                required,
                maxLength: maxLength(5000),
            },
        }
    },
    mounted() {
        this.init();
    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init () {
            this.eventId = this.$route.params.id;
            this.setIsShowSpinner(true);
            this.getEvent(this.eventId);
            this.getQuestions(this.eventId);
            this.isLoaded = true;
            this.setIsShowSpinner(false);
        },
        getEvent(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.event = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        getQuestions(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`questions/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.questions = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeQuestion() {
            if (this.$v.newQuestion.title.$invalid) {
                this.$v.newQuestion.title.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`questions`, {
                    title: this.newQuestion.title,
                    event_id: this.eventId,
                })
                .then(response => {
                    this.getQuestions(this.eventId);
                })
                .catch(error => {

                })
                .then(() => {
                    this.newQuestion.title = "";
                    this.setIsShowSpinner(false);
                });
        },
        editQuestion(question) {
            this.editId = question.id;
            this.editedQuestion =_clone(question, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedQuestion = null;
        },
        updateQuestion() {
            if (this.$v.editedQuestion.title.$invalid) {
                this.$v.editedQuestion.title.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`questions/${this.editId}`, {
                    title: this.editedQuestion.title,
                })
                .then(response => {
                    this.getQuestions(this.eventId);
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteQuestion(questionId) {
            if (window.confirm("Видалити запитання?")) {
                this.setIsShowSpinner(true);
                axios
                    .delete(`/questions/${questionId}`)
                    .then(response => {
                        this.getQuestions(this.eventId);
                    })
                    .catch(error => {
                    })
                    .then(() => {
                        this.setIsShowSpinner(false);
                    });
            }
        },
    }
}
</script>

<style scoped lang="scss">

.breadcrumb-item.link {
    cursor: pointer;
}

.content {
    background-color: rgb(241, 241, 241);
}

</style>
