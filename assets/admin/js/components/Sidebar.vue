<template>
    <div class="sidebar bg-blue-10" :class="{'closed': !isActive}">
        <div class="sidebar-label text-caption">
            Пользователи
        </div>
        <ul class="sidebar-menu">
            <!--<li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'SpecialityList'}">
                    {{$loc('specialityList')}}
                </router-link>
            </li>
            -->
        </ul>
    </div>
</template>

<script>
export default {
    name: 'Sidebar',
    props: {
        isActive: {
            type: Boolean,
        },
    },
};
</script>

