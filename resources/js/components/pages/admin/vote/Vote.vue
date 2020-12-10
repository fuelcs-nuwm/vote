<template>
    <div class="content container-fluid py-3 flex-grow-1 d-flex flex-column">
        <h2>Налаштування голосування</h2>
        <div class="row flex-grow-1 d-flex">
            <div class="col-lg-3 d-flex flex-grow-1 flex-column">
                <p>Засідання</p>
                <iframe class=" flex-grow-1" src="https://www.youtube.com/embed/xOWxhlMIQg4" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="col-lg-6">
                <div class="form-group d-flex flex-column">
                    <p>Вибір події:</p>
                    <div class="d-flex">
                        <div class="flex-grow-1 mr-3">
                            <select class="form-control">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                        <button class="btn btn-success">Активувати подію</button>
                    </div>
                </div>
                <p>Активна подія:</p>
                <div class="input-group mb-3">
                    <input
                        type="text" class="form-control"
                        placeholder="Нова подія"
                        v-model="title"
                        readonly
                    >
                    <div class="input-group-append">
                        <span class="input-group-text bg-secondary">Деактивувати</span>
                    </div>
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
export default {
    data() {
        return {
            title: 'немає активної події',
        };
    },
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);

    iframe {
        width:100%;
        height:500px;
    }
}

</style>
