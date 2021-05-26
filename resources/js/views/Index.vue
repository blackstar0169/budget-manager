<template>
    <div>
        {{message}}
    </div>
</template>
<script>
const default_layout = "default";


export default {
    name: 'Index',
    computed: {},
    data() {
        return {
            message:'Hello World'
        }
    }
};
</script>
