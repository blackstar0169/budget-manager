<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item" v-for="link in links">
                        <router-link class="nav-link" active-class="active" :to="link.href">{{link.title}}</router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Header',
    computed: {},
    data() {
        return {
            links: [
                {
                    title: "Dashboard",
                    href: "/"
                },
                {
                    title: "Incomes",
                    href: "/incomes"
                },
                {
                    title: "Future",
                    href: "/future"
                }
            ]
        }
    }
};
</script>
