module.exports = {
  purge: [],
  theme: {
    screens: {
			xs: '350px',
      sm: '540px',
      md: '768px',
      lg: '980px',
			xl: '1210px'
    },
    extend: {
      colors: {
        transparent: 'transparent',
        primary: '#00a6d7',
        secondary: '#7d5e3a',
				lightgrey: '#f3f3f3',
				grey: '#b4b4b4',
				darkgrey: '#5b5b5b'
			},
			maxWidth: {
				'xxs': '12rem'
			}
		},
  },
  variants: {},
	plugins: [],
  corePlugins: {
    container: false,
  }
}
