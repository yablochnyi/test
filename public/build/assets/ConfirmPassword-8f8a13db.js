import{v as d,c as l,w as t,o as m,a as o,u as a,X as c,b as e,d as p,n as u,e as f}from"./app-c265b612.js";import{_}from"./GuestLayout-9b39a1e7.js";import{_ as w,a as b,b as x}from"./TextInput-7e1df443.js";import{_ as g}from"./PrimaryButton-15ee8b26.js";import"./ApplicationLogo-97c9e60b.js";const h=e("div",{class:"mb-4 text-sm text-gray-600 dark:text-gray-400"}," This is a secure area of the application. Please confirm your password before continuing. ",-1),v=["onSubmit"],y={class:"flex justify-end mt-4"},S={__name:"ConfirmPassword",setup(V){const s=d({password:""}),i=()=>{s.post(route("password.confirm"),{onFinish:()=>s.reset()})};return(C,r)=>(m(),l(_,null,{default:t(()=>[o(a(c),{title:"Confirm Password"}),h,e("form",{onSubmit:f(i,["prevent"])},[e("div",null,[o(w,{for:"password",value:"Password"}),o(b,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:a(s).password,"onUpdate:modelValue":r[0]||(r[0]=n=>a(s).password=n),required:"",autocomplete:"current-password",autofocus:""},null,8,["modelValue"]),o(x,{class:"mt-2",message:a(s).errors.password},null,8,["message"])]),e("div",y,[o(g,{class:u(["ml-4",{"opacity-25":a(s).processing}]),disabled:a(s).processing},{default:t(()=>[p(" Confirm ")]),_:1},8,["class","disabled"])])],40,v)]),_:1}))}};export{S as default};
