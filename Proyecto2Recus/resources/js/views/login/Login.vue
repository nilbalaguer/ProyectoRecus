<template>
    <div class="d-flex justify-content-start">

        <div class="w-50 vh-100 d-flex justify-content-center align-items-center d-none d-md-flex">
            <div class="w-75 h-100 m-auto advertisment-menu" style="background-color: var(--background4);">
                <h2 class="brandName w-100 text-center pt-5">{{ $t('brandName') }}.</h2>
            </div>
        </div>

        <div class="w-100 w-md-50 loginContainer">
            <div class="container">
                <div class="row justify-content-center my-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm p-3">
                            <div class="card-body iniciosession">
                                <h2 class="mb-4">Inicio de Sesión</h2>
                                <form @submit.prevent="submitLogin">
                                    <div>

                                        <div class="pd-6"></div>

                                        <div class="mb-3 w-75 m-auto">
                                            <label for="email" class="form-label w-100" style="text-align: left !important;">{{ $t('email') }}</label>

                                            <div class="d-flex justify-content-start p-0 m-0">
                                                <input v-model="loginForm.email" id="email" type="email"
                                                    class="form-control form-control-sm" required autofocus
                                                    autocomplete="username" placeholder="electronicmail@email.com">
                                                <p>✅</p>
                                            </div>

                                            <div v-if="validationErrors?.email" class="text-danger mt-1">
                                                <div v-for="message in validationErrors.email" :key="message">{{ message}}
                                            </div>
                                            
                                            </div>
                                        </div>

                                        <div class="mb-3  w-75 m-auto">
                                            <label for="password" class="form-label w-100" style="text-align: left !important;">{{ $t('password') }}</label>

                                            <div class="d-flex justify-content-start  p-0 m-0">
                                                <input v-model="loginForm.password" id="password" type="password"
                                                    class="form-control form-control-sm" required
                                                    autocomplete="current-password" placeholder="********">
                                                <p>✅</p>
                                            </div>

                                            <div v-if="validationErrors?.password" class="text-danger mt-1">
                                                <div v-for="message in validationErrors.password" :key="message">{{
                                                    message }}</div>
                                            </div>
                                        </div>

                                        <div class="form-check mb-3  w-75 m-auto">
                                            <input class="form-check-input checkInput" type="checkbox" name="remember"
                                                v-model="loginForm.remember" id="recuerdameinput">
                                            <label class="form-check-label checkLabel" for="recuerdameinput">
                                                {{ $t('remember_me') }}
                                            </label>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn btn-primary btn-sm primary-button" :class="{ 'opacity-25': processing }"
                                                :disabled="processing">
                                                <span v-if="processing" class="spinner-border spinner-border-sm"></span>
                                                {{ $t('login') }}
                                            </button>
                                        </div>
                                    </div>
                                    <router-link :to="{name: 'auth.forgot-password'}">{{ $t('forgot_password')}}</router-link>
                                    <br>
                                    <router-link :to="{name: 'auth.register'}">{{ $t('notregisted') }}</router-link>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import useAuth from '@/composables/auth'

const { loginForm, validationErrors, processing, submitLogin } = useAuth();
</script>