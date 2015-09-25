(function (window, document, exportName, undefined) {
	'use strict';

	var VENDOR_PREFIXES = ['', 'webkit', 'moz', 'MS', 'ms', 'o'];
	var TEST_ELEMENT = document.createElement('div');

	var TYPE_FUNCTION = 'function';

	var round = Math.round;
	var abs = Math.abs;
	var now = Date.now;

	/**
	 * set a timeout with a given scope
	 * @param {Function} fn
	 * @param {Number} timeout
	 * @param {Object} context
	 * @returns {number}
	 */
	function setTimeoutContext(fn, timeout, context) {
		return setTimeout(bindFn(fn, context), timeout);
	}

	/**
	 * if the argument is an array, we want to execute the fn on each entry
	 * if it aint an array we don't want to do a thing.
	 * this is used by all the methods that accept a single and array argument.
	 * @param {*|Array} arg
	 * @param {String} fn
	 * @param {Object} [context]
	 * @returns {Boolean}
	 */
	function invokeArrayArg(arg, fn, context) {
		if (Array.isArray(arg)) {
			each(arg, context[fn], context);
			return true;
		}
		return false;
	}

	/**
	 * walk objects and arrays
	 * @param {Object} obj
	 * @param {Function} iterator
	 * @param {Object} context
	 */
	function each(obj, iterator, context) {
		var i;

		if (!obj) {
			return;
		}

		if (obj.forEach) {
			obj.forEach(iterator, context);
		} else if (obj.length !== undefined) {
			i = 0;
			while (i < obj.length) {
				iterator.call(context, obj[i], i, obj);
				i++;
			}
		} else {
			for (i in obj) {
				obj.hasOwnProperty(i) && iterator.call(context, obj[i], i, obj);
			}
		}
	}

	/**
	 * extend object.
	 * means that properties in dest will be overwritten by the ones in src.
	 * @param {Object} dest
	 * @param {Object} src
	 * @param {Boolean} [merge]
	 * @returns {Object} dest
	 */
	function extend(dest, src, merge) {
		var keys = Object.keys(src);
		var i = 0;
		while (i < keys.length) {
			if (!merge || (merge && dest[keys[i]] === undefined)) {
				dest[keys[i]] = src[keys[i]];
			}
			i++;
		}
		return dest;
	}

	/**
	 * merge the values from src in the dest.
	 * means that properties that exist in dest will not be overwritten by src
	 * @param {Object} dest
	 * @param {Object} src
	 * @returns {Object} dest
	 */
	function merge(dest, src) {
		return extend(dest, src, true);
	}

	/**
	 * simple class inheritance
	 * @param {Function} child
	 * @param {Function} base
	 * @param {Object} [properties]
	 */
	function inherit(child, base, properties) {
		var baseP = base.prototype,
			childP;

		childP = child.prototype = Object.create(baseP);
		childP.constructor = child;
		childP._super = baseP;

		if (properties) {
			extend(childP, properties);
		}
	}

	/**
	 * simple function bind
	 * @param {Function} fn
	 * @param {Object} context
	 * @returns {Function}
	 */
	function bindFn(fn, context) {
		return function boundFn() {
			return fn.apply(context, arguments);
		};
	}

	/**
	 * let a boolean value also be a function that must return a boolean
	 * this first item in args will be used as the context
	 * @param {Boolean|Function} val
	 * @param {Array} [args]
	 * @returns {Boolean}
	 */
	function boolOrFn(val, args) {
		if (typeof val == TYPE_FUNCTION) {
			return val.apply(args ? args[0] || undefined : undefined, args);
		}
		return val;
	}

	/**
	 * use the val2 when val1 is undefined
	 * @param {*} val1
	 * @param {*} val2
	 * @returns {*}
	 */
	function ifUndefined(val1, val2) {
		return (val1 === undefined) ? val2 : val1;
	}

	/**
	 * addEventListener with multiple events at once
	 * @param {EventTarget} target
	 * @param {String} types
	 * @param {Function} handler
	 */
	function addEventListeners(target, types, handler) {
		each(splitStr(types), function (type) {
			target.addEventListener(type, handler, false);
		});
	}

	/**
	 * removeEventListener with multiple events at once
	 * @param {EventTarget} target
	 * @param {String} types
	 * @param {Function} handler
	 */
	function removeEventListeners(target, types, handler) {
		each(splitStr(types), function (type) {
			target.removeEventListener(type, handler, false);
		});
	}

	/**
	 * find if a node is in the given parent
	 * @method hasParent
	 * @param {HTMLElement} node
	 * @param {HTMLElement} parent
	 * @return {Boolean} found
	 */
	function hasParent(node, parent) {
		while (node) {
			if (node == parent) {
				return true;
			}
			node = node.parentNode;
		}
		return false;
	}

	/**
	 * small indexOf wrapper
	 * @param {String} str
	 * @param {String} find
	 * @returns {Boolean} found
	 */
	function inStr(str, find) {
		return str.indexOf(find) > -1;
	}

	/**
	 * split string on whitespace
	 * @param {String} str
	 * @returns {Array} words
	 */
	function splitStr(str) {
		return str.trim().split(/\s+/g);
	}

	/**
	 * find if a array contains the object using indexOf or a simple polyFill
	 * @param {Array} src
	 * @param {String} find
	 * @param {String} [findByKey]
	 * @return {Boolean|Number} false when not found, or the index
	 */
	function inArray(src, find, findByKey) {
		if (src.indexOf && !findByKey) {
			return src.indexOf(find);
		} else {
			var i = 0;
			while (i < src.length) {
				if ((findByKey && src[i][findByKey] == find) || (!findByKey && src[i] === find)) {
					return i;
				}
				i++;
			}
			return -1;
		}
	}

	/**
	 * convert array-like objects to real arrays
	 * @param {Object} obj
	 * @returns {Array}
	 */
	function toArray(obj) {
		return Array.prototype.slice.call(obj, 0);
	}

	/**
	 * unique array with objects based on a key (like 'id') or just by the array's value
	 * @param {Array} src [{id:1},{id:2},{id:1}]
	 * @param {String} [key]
	 * @param {Boolean} [sort=False]
	 * @returns {Array} [{id:1},{id:2}]
	 */
	function uniqueArray(src, key, sort) {
		var results = [];
		var values = [];
		var i = 0;

		while (i < src.length) {
			var val = key ? src[i][key] : src[i];
			if (inArray(values, val) < 0) {
				results.push(src[i]);
			}
			values[i] = val;
			i++;
		}

		if (sort) {
			if (!key) {
				results = results.sort();
			} else {
				results = results.sort(function sortUniqueArray(a, b) {
					return a[key] > b[key];
				});
			}
		}

		return results;
	}

	/**
	 * get the prefixed property
	 * @param {Object} obj
	 * @param {String} property
	 * @returns {String|Undefined} prefixed
	 */
	function prefixed(obj, property) {
		var prefix, prop;
		var camelProp = property[0].toUpperCase() + property.slice(1);

		var i = 0;
		while (i < VENDOR_PREFIXES.length) {
			prefix = VENDOR_PREFIXES[i];
			prop = (prefix) ? prefix + camelProp : property;

			if (prop in obj) {
				return prop;
			}
			i++;
		}
		return undefined;
	}

	/**
	 * get a unique id
	 * @returns {number} uniqueId
	 */
	var _uniqueId = 1;

	function uniqueId() {
		return _uniqueId++;
	}

	/**
	 * get the window object of an element
	 * @param {HTMLElement} element
	 * @returns {DocumentView|Window}
	 */
	function getWindowForElement(element) {
		var doc = element.ownerDocument;
		return (doc.defaultView || doc.parentWindow);
	}

	var MOBILE_REGEX = /mobile|tablet|ip(ad|hone|od)|android/i;

	var SUPPORT_TOUCH = ('ontouchstart' in window);
	var SUPPORT_POINTER_EVENTS = prefixed(window, 'PointerEvent') !== undefined;
	var SUPPORT_ONLY_TOUCH = SUPPORT_TOUCH && MOBILE_REGEX.test(navigator.userAgent);

	var INPUT_TYPE_TOUCH = 'touch';
	var INPUT_TYPE_PEN = 'pen';
	var INPUT_TYPE_MOUSE = 'mouse';
	var INPUT_TYPE_KINECT = 'kinect';

	var COMPUTE_INTERVAL = 25;

	var INPUT_START = 1;
	var INPUT_MOVE = 2;
	var INPUT_END = 4;
	var INPUT_CANCEL = 8;

	var DIRECTION_NONE = 1;
	var DIRECTION_LEFT = 2;
	var DIRECTION_RIGHT = 4;
	var DIRECTION_UP = 8;
	var DIRECTION_DOWN = 16;

	var DIRECTION_HORIZONTAL = DIRECTION_LEFT | DIRECTION_RIGHT;
	var DIRECTION_VERTICAL = DIRECTION_UP | DIRECTION_DOWN;
	var DIRECTION_ALL = DIRECTION_HORIZONTAL | DIRECTION_VERTICAL;

	var PROPS_XY = ['x', 'y'];
	var PROPS_CLIENT_XY = ['clientX', 'clientY'];

	/**
	 * create new input type manager
	 * @param {Manager} manager
	 * @param {Function} callback
	 * @returns {Input}
	 * @constructor
	 */
	function Input(manager, callback) {
		var self = this;
		this.manager = manager;
		this.callback = callback;
		this.element = manager.element;
		this.target = manager.options.inputTarget;

		// smaller wrapper around the handler, for the scope and the enabled state of the manager,
		// so when disabled the input events are completely bypassed.
		this.domHandler = function (ev) {
			if (boolOrFn(manager.options.enable, [manager])) {
				self.handler(ev);
			}
		};

		this.init();

	}

	Input.prototype = {
		/**
		 * should handle the inputEvent data and trigger the callback
		 * @virtual
		 */
		handler: function () {
		},

		/**
		 * bind the events
		 */
		init: function () {
			this.evEl && addEventListeners(this.element, this.evEl, this.domHandler);
			this.evTarget && addEventListeners(this.target, this.evTarget, this.domHandler);
			this.evWin && addEventListeners(getWindowForElement(this.element), this.evWin, this.domHandler);
		},

		/**
		 * unbind the events
		 */
		destroy: function () {
			this.evEl && removeEventListeners(this.element, this.evEl, this.domHandler);
			this.evTarget && removeEventListeners(this.target, this.evTarget, this.domHandler);
			this.evWin && removeEventListeners(getWindowForElement(this.element), this.evWin, this.domHandler);
		}
	};

	/**
	 * create new input type manager
	 * called by the Manager constructor
	 * @param {Hammer} manager
	 * @returns {Input}
	 */
	function createInputInstance(manager) {
		var Type;
		var inputClass = manager.options.inputClass;

		if (inputClass) {
			Type = inputClass;
		} else if (SUPPORT_POINTER_EVENTS) {
			Type = PointerEventInput;
		} else if (SUPPORT_ONLY_TOUCH) {
			Type = TouchInput;
		} else if (!SUPPORT_TOUCH) {
			Type = MouseInput;
		} else {
			Type = TouchMouseInput;
		}
		return new (Type)(manager, inputHandler);
	}

	/**
	 * handle input events
	 * @param {Manager} manager
	 * @param {String} eventType
	 * @param {Object} input
	 */
	function inputHandler(manager, eventType, input) {
		var pointersLen = input.pointers.length;
		var changedPointersLen = input.changedPointers.length;
		var isFirst = (eventType & INPUT_START && (pointersLen - changedPointersLen === 0));
		var isFinal = (eventType & (INPUT_END | INPUT_CANCEL) && (pointersLen - changedPointersLen === 0));

		input.isFirst = !!isFirst;
		input.isFinal = !!isFinal;

		if (isFirst) {
			manager.session = {};
		}

		// source event is the normalized value of the domEvents
		// like 'touchstart, mouseup, pointerdown'
		input.eventType = eventType;

		// compute scale, rotation etc
		computeInputData(manager, input);

		// emit secret event
		manager.emit('hammer.input', input);

		manager.recognize(input);
		manager.session.prevInput = input;
	}

	/**
	 * extend the data with some usable properties like scale, rotate, velocity etc
	 * @param {Object} manager
	 * @param {Object} input
	 */
	function computeInputData(manager, input) {
		var session = manager.session;
		var pointers = input.pointers;
		var pointersLength = pointers.length;

		// store the first input to calculate the distance and direction
		if (!session.firstInput) {
			session.firstInput = simpleCloneInputData(input);
		}

		// to compute scale and rotation we need to store the multiple touches
		if (pointersLength > 1 && !session.firstMultiple) {
			session.firstMultiple = simpleCloneInputData(input);
		} else if (pointersLength === 1) {
			session.firstMultiple = false;
		}

		var firstInput = session.firstInput;
		var firstMultiple = session.firstMultiple;
		var offsetCenter = firstMultiple ? firstMultiple.center : firstInput.center;

		var center = input.center = getCenter(pointers);
		input.timeStamp = now();
		input.deltaTime = input.timeStamp - firstInput.timeStamp;

		input.angle = getAngle(offsetCenter, center);
		input.distance = getDistance(offsetCenter, center);

		computeDeltaXY(session, input);
		input.offsetDirection = getDirection(input.deltaX, input.deltaY);

		input.scale = firstMultiple ? getScale(firstMultiple.pointers, pointers) : 1;
		input.rotation = firstMultiple ? getRotation(firstMultiple.pointers, pointers) : 0;

		computeIntervalInputData(session, input);

		// find the correct target
		var target = manager.element;
		if (hasParent(input.srcEvent.target, target)) {
			target = input.srcEvent.target;
		}
		input.target = target;
	}

	function computeDeltaXY(session, input) {
		var center = input.center;
		var offset = session.offsetDelta || {};
		var prevDelta = session.prevDelta || {};
		var prevInput = session.prevInput || {};

		if (input.eventType === INPUT_START || prevInput.eventType === INPUT_END) {
			prevDelta = session.prevDelta = {
				x: prevInput.deltaX || 0,
				y: prevInput.deltaY || 0
			};

			offset = session.offsetDelta = {
				x: center.x,
				y: center.y
			};
		}

		input.deltaX = prevDelta.x + (center.x - offset.x);
		input.deltaY = prevDelta.y + (center.y - offset.y);
	}

	/**
	 * velocity is calculated every x ms
	 * @param {Object} session
	 * @param {Object} input
	 */
	function computeIntervalInputData(session, input) {
		var last = session.lastInterval || input,
			deltaTime = input.timeStamp - last.timeStamp,
			velocity, velocityX, velocityY, direction;

		if (input.eventType != INPUT_CANCEL && (deltaTime > COMPUTE_INTERVAL || last.velocity === undefined)) {
			var deltaX = last.deltaX - input.deltaX;
			var deltaY = last.deltaY - input.deltaY;

			var v = getVelocity(deltaTime, deltaX, deltaY);
			velocityX = v.x;
			velocityY = v.y;
			velocity = (abs(v.x) > abs(v.y)) ? v.x : v.y;
			direction = getDirection(deltaX, deltaY);

			session.lastInterval = input;
		} else {
			// use latest velocity info if it doesn't overtake a minimum period
			velocity = last.velocity;
			velocityX = last.velocityX;
			velocityY = last.velocityY;
			direction = last.direction;
		}

		input.velocity = velocity;
		input.velocityX = velocityX;
		input.velocityY = velocityY;
		input.direction = direction;
	}

	/**
	 * create a simple clone from the input used for storage of firstInput and firstMultiple
	 * @param {Object} input
	 * @returns {Object} clonedInputData
	 */
	function simpleCloneInputData(input) {
		// make a simple copy of the pointers because we will get a reference if we don't
		// we only need clientXY for the calculations
		var pointers = [];
		var i = 0;
		while (i < input.pointers.length) {
			pointers[i] = {
				clientX: round(input.pointers[i].clientX),
				clientY: round(input.pointers[i].clientY)
			};
			i++;
		}

		return {
			timeStamp: now(),
			pointers: pointers,
			center: getCenter(pointers),
			deltaX: input.deltaX,
			deltaY: input.deltaY
		};
	}

	/**
	 * get the center of all the pointers
	 * @param {Array} pointers
	 * @return {Object} center contains `x` and `y` properties
	 */
	function getCenter(pointers) {
		var pointersLength = pointers.length;

		// no need to loop when only one touch
		if (pointersLength === 1) {
			return {
				x: round(pointers[0].clientX),
				y: round(pointers[0].clientY)
			};
		}

		var x = 0, y = 0, i = 0;
		while (i < pointersLength) {
			x += pointers[i].clientX;
			y += pointers[i].clientY;
			i++;
		}

		return {
			x: round(x / pointersLength),
			y: round(y / pointersLength)
		};
	}

	/**
	 * calculate the velocity between two points. unit is in px per ms.
	 * @param {Number} deltaTime
	 * @param {Number} x
	 * @param {Number} y
	 * @return {Object} velocity `x` and `y`
	 */
	function getVelocity(deltaTime, x, y) {
		return {
			x: x / deltaTime || 0,
			y: y / deltaTime || 0
		};
	}

	/**
	 * get the direction between two points
	 * @param {Number} x
	 * @param {Number} y
	 * @return {Number} direction
	 */
	function getDirection(x, y) {
		if (x === y) {
			return DIRECTION_NONE;
		}

		if (abs(x) >= abs(y)) {
			return x > 0 ? DIRECTION_LEFT : DIRECTION_RIGHT;
		}
		return y > 0 ? DIRECTION_UP : DIRECTION_DOWN;
	}

	/**
	 * calculate the absolute distance between two points
	 * @param {Object} p1 {x, y}
	 * @param {Object} p2 {x, y}
	 * @param {Array} [props] containing x and y keys
	 * @return {Number} distance
	 */
	function getDistance(p1, p2, props) {
		if (!props) {
			props = PROPS_XY;
		}
		var x = p2[props[0]] - p1[props[0]],
			y = p2[props[1]] - p1[props[1]];

		return Math.sqrt((x * x) + (y * y));
	}

	/**
	 * calculate the angle between two coordinates
	 * @param {Object} p1
	 * @param {Object} p2
	 * @param {Array} [props] containing x and y keys
	 * @return {Number} angle
	 */
	function getAngle(p1, p2, props) {
		if (!props) {
			props = PROPS_XY;
		}
		var x = p2[props[0]] - p1[props[0]],
			y = p2[props[1]] - p1[props[1]];
		return Math.atan2(y, x) * 180 / Math.PI;
	}

	/**
	 * calculate the rotation degrees between two pointersets
	 * @param {Array} start array of pointers
	 * @param {Array} end array of pointers
	 * @return {Number} rotation
	 */
	function getRotation(start, end) {
		return getAngle(end[1], end[0], PROPS_CLIENT_XY) - getAngle(start[1], start[0], PROPS_CLIENT_XY);
	}

	/**
	 * calculate the scale factor between two pointersets
	 * no scale is 1, and goes down to 0 when pinched together, and bigger when pinched out
	 * @param {Array} start array of pointers
	 * @param {Array} end array of pointers
	 * @return {Number} scale
	 */
	function getScale(start, end) {
		return getDistance(end[0], end[1], PROPS_CLIENT_XY) / getDistance(start[0], start[1], PROPS_CLIENT_XY);
	}

	var MOUSE_INPUT_MAP = {
		mousedown: INPUT_START,
		mousemove: INPUT_MOVE,
		mouseup: INPUT_END
	};

	var MOUSE_ELEMENT_EVENTS = 'mousedown';
	var MOUSE_WINDOW_EVENTS = 'mousemove mouseup';

	/**
	 * Mouse events input
	 * @constructor
	 * @extends Input
	 */
	function MouseInput() {
		this.evEl = MOUSE_ELEMENT_EVENTS;
		this.evWin = MOUSE_WINDOW_EVENTS;

		this.allow = true; // used by Input.TouchMouse to disable mouse events
		this.pressed = false; // mousedown state

		Input.apply(this, arguments);
	}

	inherit(MouseInput, Input, {
		/**
		 * handle mouse events
		 * @param {Object} ev
		 */
		handler: function MEhandler(ev) {
			var eventType = MOUSE_INPUT_MAP[ev.type];

			// on start we want to have the left mouse button down
			if (eventType & INPUT_START && ev.button === 0) {
				this.pressed = true;
			}

			if (eventType & INPUT_MOVE && ev.which !== 1) {
				eventType = INPUT_END;
			}

			// mouse must be down, and mouse events are allowed (see the TouchMouse input)
			if (!this.pressed || !this.allow) {
				return;
			}

			if (eventType & INPUT_END) {
				this.pressed = false;
			}

			this.callback(this.manager, eventType, {
				pointers: [ev],
				changedPointers: [ev],
				pointerType: INPUT_TYPE_MOUSE,
				srcEvent: ev
			});
		}
	});

	var POINTER_INPUT_MAP = {
		pointerdown: INPUT_START,
		pointermove: INPUT_MOVE,
		pointerup: INPUT_END,
		pointercancel: INPUT_CANCEL,
		pointerout: INPUT_CANCEL
	};

// in IE10 the pointer types is defined as an enum
	var IE10_POINTER_TYPE_ENUM = {
		2: INPUT_TYPE_TOUCH,
		3: INPUT_TYPE_PEN,
		4: INPUT_TYPE_MOUSE,
		5: INPUT_TYPE_KINECT // see https://twitter.com/jacobrossi/status/480596438489890816
	};

	var POINTER_ELEMENT_EVENTS = 'pointerdown';
	var POINTER_WINDOW_EVENTS = 'pointermove pointerup pointercancel';

// IE10 has prefixed support, and case-sensitive
	if (window.MSPointerEvent) {
		POINTER_ELEMENT_EVENTS = 'MSPointerDown';
		POINTER_WINDOW_EVENTS = 'MSPointerMove MSPointerUp MSPointerCancel';
	}

	/**
	 * Pointer events input
	 * @constructor
	 * @extends Input
	 */
	function PointerEventInput() {
		this.evEl = POINTER_ELEMENT_EVENTS;
		this.evWin = POINTER_WINDOW_EVENTS;

		Input.apply(this, arguments);

		this.store = (this.manager.session.pointerEvents = []);
	}

	inherit(PointerEventInput, Input, {
		/**
		 * handle mouse events
		 * @param {Object} ev
		 */
		handler: function PEhandler(ev) {
			var store = this.store;
			var removePointer = false;

			var eventTypeNormalized = ev.type.toLowerCase().replace('ms', '');
			var eventType = POINTER_INPUT_MAP[eventTypeNormalized];
			var pointerType = IE10_POINTER_TYPE_ENUM[ev.pointerType] || ev.pointerType;

			var isTouch = (pointerType == INPUT_TYPE_TOUCH);

			// get index of the event in the store
			var storeIndex = inArray(store, ev.pointerId, 'pointerId');

			// start and mouse must be down
			if (eventType & INPUT_START && (ev.button === 0 || isTouch)) {
				if (storeIndex < 0) {
					store.push(ev);
					storeIndex = store.length - 1;
				}
			} else if (eventType & (INPUT_END | INPUT_CANCEL)) {
				removePointer = true;
			}

			// it not found, so the pointer hasn't been down (so it's probably a hover)
			if (storeIndex < 0) {
				return;
			}

			// update the event in the store
			store[storeIndex] = ev;

			this.callback(this.manager, eventType, {
				pointers: store,
				changedPointers: [ev],
				pointerType: pointerType,
				srcEvent: ev
			});

			if (removePointer) {
				// remove from the store
				store.splice(storeIndex, 1);
			}
		}
	});

	var SINGLE_TOUCH_INPUT_MAP = {
		touchstart: INPUT_START,
		touchmove: INPUT_MOVE,
		touchend: INPUT_END,
		touchcancel: INPUT_CANCEL
	};

	var SINGLE_TOUCH_TARGET_EVENTS = 'touchstart';
	var SINGLE_TOUCH_WINDOW_EVENTS = 'touchstart touchmove touchend touchcancel';

	/**
	 * Touch events input
	 * @constructor
	 * @extends Input
	 */
	function SingleTouchInput() {
		this.evTarget = SINGLE_TOUCH_TARGET_EVENTS;
		this.evWin = SINGLE_TOUCH_WINDOW_EVENTS;
		this.started = false;

		Input.apply(this, arguments);
	}

	inherit(SingleTouchInput, Input, {
		handler: function TEhandler(ev) {
			var type = SINGLE_TOUCH_INPUT_MAP[ev.type];

			// should we handle the touch events?
			if (type === INPUT_START) {
				this.started = true;
			}

			if (!this.started) {
				return;
			}

			var touches = normalizeSingleTouches.call(this, ev, type);

			// when done, reset the started state
			if (type & (INPUT_END | INPUT_CANCEL) && touches[0].length - touches[1].length === 0) {
				this.started = false;
			}

			this.callback(this.manager, type, {
				pointers: touches[0],
				changedPointers: touches[1],
				pointerType: INPUT_TYPE_TOUCH,
				srcEvent: ev
			});
		}
	});

	/**
	 * @this {TouchInput}
	 * @param {Object} ev
	 * @param {Number} type flag
	 * @returns {undefined|Array} [all, changed]
	 */
	function normalizeSingleTouches(ev, type) {
		var all = toArray(ev.touches);
		var changed = toArray(ev.changedTouches);

		if (type & (INPUT_END | INPUT_CANCEL)) {
			all = uniqueArray(all.concat(changed), 'identifier', true);
		}

		return [all, changed];
	}

	var TOUCH_INPUT_MAP = {
		touchstart: INPUT_START,
		touchmove: INPUT_MOVE,
		touchend: INPUT_END,
		touchcancel: INPUT_CANCEL
	};

	var TOUCH_TARGET_EVENTS = 'touchstart touchmove touchend touchcancel';

	/**
	 * Multi-user touch events input
	 * @constructor
	 * @extends Input
	 */
	function TouchInput() {
		this.evTarget = TOUCH_TARGET_EVENTS;
		this.targetIds = {};

		Input.apply(this, arguments);
	}

	inherit(TouchInput, Input, {
		handler: function MTEhandler(ev) {
			var type = TOUCH_INPUT_MAP[ev.type];
			var touches = getTouches.call(this, ev, type);
			if (!touches) {
				return;
			}

			this.callback(this.manager, type, {
				pointers: touches[0],
				changedPointers: touches[1],
				pointerType: INPUT_TYPE_TOUCH,
				srcEvent: ev
			});
		}
	});

	/**
	 * @this {TouchInput}
	 * @param {Object} ev
	 * @param {Number} type flag
	 * @returns {undefined|Array} [all, changed]
	 */
	function getTouches(ev, type) {
		var allTouches = toArray(ev.touches);
		var targetIds = this.targetIds;

		// when there is only one touch, the process can be simplified
		if (type & (INPUT_START | INPUT_MOVE) && allTouches.length === 1) {
			targetIds[allTouches[0].identifier] = true;
			return [allTouches, allTouches];
		}

		var i,
			targetTouches,
			changedTouches = toArray(ev.changedTouches),
			changedTargetTouches = [],
			target = this.target;

		// get target touches from touches
		targetTouches = allTouches.filter(function (touch) {
			return hasParent(touch.target, target);
		});

		// collect touches
		if (type === INPUT_START) {
			i = 0;
			while (i < targetTouches.length) {
				targetIds[targetTouches[i].identifier] = true;
				i++;
			}
		}

		// filter changed touches to only contain touches that exist in the collected target ids
		i = 0;
		while (i < changedTouches.length) {
			if (targetIds[changedTouches[i].identifier]) {
				changedTargetTouches.push(changedTouches[i]);
			}

			// cleanup removed touches
			if (type & (INPUT_END | INPUT_CANCEL)) {
				delete targetIds[changedTouches[i].identifier];
			}
			i++;
		}

		if (!changedTargetTouches.length) {
			return;
		}

		return [
			// merge targetTouches with changedTargetTouches so it contains ALL touches, including 'end' and 'cancel'
			uniqueArray(targetTouches.concat(changedTargetTouches), 'identifier', true),
			changedTargetTouches
		];
	}

	/**
	 * Combined touch and mouse input
	 *
	 * Touch has a higher priority then mouse, and while touching no mouse events are allowed.
	 * This because touch devices also emit mouse events while doing a touch.
	 *
	 * @constructor
	 * @extends Input
	 */
	function TouchMouseInput() {
		Input.apply(this, arguments);

		var handler = bindFn(this.handler, this);
		this.touch = new TouchInput(this.manager, handler);
		this.mouse = new MouseInput(this.manager, handler);
	}

	inherit(TouchMouseInput, Input, {
		/**
		 * handle mouse and touch events
		 * @param {Hammer} manager
		 * @param {String} inputEvent
		 * @param {Object} inputData
		 */
		handler: function TMEhandler(manager, inputEvent, inputData) {
			var isTouch = (inputData.pointerType == INPUT_TYPE_TOUCH),
				isMouse = (inputData.pointerType == INPUT_TYPE_MOUSE);

			// when we're in a touch event, so  block all upcoming mouse events
			// most mobile browser also emit mouseevents, right after touchstart
			if (isTouch) {
				this.mouse.allow = false;
			} else if (isMouse && !this.mouse.allow) {
				return;
			}

			// reset the allowMouse when we're done
			if (inputEvent & (INPUT_END | INPUT_CANCEL)) {
				this.mouse.allow = true;
			}

			this.callback(manager, inputEvent, inputData);
		},

		/**
		 * remove the event listeners
		 */
		destroy: function destroy() {
			this.touch.destroy();
			this.mouse.destroy();
		}
	});

	var PREFIXED_TOUCH_ACTION = prefixed(TEST_ELEMENT.style, 'touchAction');
	var NATIVE_TOUCH_ACTION = PREFIXED_TOUCH_ACTION !== undefined;

// magical touchAction value
	var TOUCH_ACTION_COMPUTE = 'compute';
	var TOUCH_ACTION_AUTO = 'auto';
	var TOUCH_ACTION_MANIPULATION = 'manipulation'; // not implemented
	var TOUCH_ACTION_NONE = 'none';
	var TOUCH_ACTION_PAN_X = 'pan-x';
	var TOUCH_ACTION_PAN_Y = 'pan-y';

	/**
	 * Touch Action
	 * sets the touchAction property or uses the js alternative
	 * @param {Manager} manager
	 * @param {String} value
	 * @constructor
	 */
	function TouchAction(manager, value) {
		this.manager = manager;
		this.set(value);
	}

	TouchAction.prototype = {
		/**
		 * set the touchAction value on the element or enable the polyfill
		 * @param {String} value
		 */
		set: function (value) {
			// find out the touch-action by the event handlers
			if (value == TOUCH_ACTION_COMPUTE) {
				value = this.compute();
			}

			if (NATIVE_TOUCH_ACTION) {
				this.manager.element.style[PREFIXED_TOUCH_ACTION] = value;
			}
			this.actions = value.toLowerCase().trim();
		},

		/**
		 * just re-set the touchAction value
		 */
		update: function () {
			this.set(this.manager.options.touchAction);
		},

		/**
		 * compute the value for the touchAction property based on the recognizer's settings
		 * @returns {String} value
		 */
		compute: function () {
			var actions = [];
			each(this.manager.recognizers, function (recognizer) {
				if (boolOrFn(recognizer.options.enable, [recognizer])) {
					actions = actions.concat(recognizer.getTouchAction());
				}
			});
			return cleanTouchActions(actions.join(' '));
		},

		/**
		 * this method is called on each input cycle and provides the preventing of the browser behavior
		 * @param {Object} input
		 */
		preventDefaults: function (input) {
			// not needed with native support for the touchAction property
			if (NATIVE_TOUCH_ACTION) {
				return;
			}

			var srcEvent = input.srcEvent;
			var direction = input.offsetDirection;

			// if the touch action did prevented once this session
			if (this.manager.session.prevented) {
				srcEvent.preventDefault();
				return;
			}

			var actions = this.actions;
			var hasNone = inStr(actions, TOUCH_ACTION_NONE);
			var hasPanY = inStr(actions, TOUCH_ACTION_PAN_Y);
			var hasPanX = inStr(actions, TOUCH_ACTION_PAN_X);

			if (hasNone ||
				(hasPanY && direction & DIRECTION_HORIZONTAL) ||
				(hasPanX && direction & DIRECTION_VERTICAL)) {
				return this.preventSrc(srcEvent);
			}
		},

		/**
		 * call preventDefault to prevent the browser's default behavior (scrolling in most cases)
		 * @param {Object} srcEvent
		 */
		preventSrc: function (srcEvent) {
			this.manager.session.prevented = true;
			srcEvent.preventDefault();
		}
	};

	/**
	 * when the touchActions are collected they are not a valid value, so we need to clean things up. *
	 * @param {String} actions
	 * @returns {*}
	 */
	function cleanTouchActions(actions) {
		// none
		if (inStr(actions, TOUCH_ACTION_NONE)) {
			return TOUCH_ACTION_NONE;
		}

		var hasPanX = inStr(actions, TOUCH_ACTION_PAN_X);
		var hasPanY = inStr(actions, TOUCH_ACTION_PAN_Y);

		// pan-x and pan-y can be combined
		if (hasPanX && hasPanY) {
			return TOUCH_ACTION_PAN_X + ' ' + TOUCH_ACTION_PAN_Y;
		}

		// pan-x OR pan-y
		if (hasPanX || hasPanY) {
			return hasPanX ? TOUCH_ACTION_PAN_X : TOUCH_ACTION_PAN_Y;
		}

		// manipulation
		if (inStr(actions, TOUCH_ACTION_MANIPULATION)) {
			return TOUCH_ACTION_MANIPULATION;
		}

		return TOUCH_ACTION_AUTO;
	}

	/**
	 * Recognizer flow explained; *
	 * All recognizers have the initial state of POSSIBLE when a input session starts.
	 * The definition of a input session is from the first input until the last input, with all it's movement in it. *
	 * Example session for mouse-input: mousedown -> mousemove -> mouseup
	 *
	 * On each recognizing cycle (see Manager.recognize) the .recognize() method is executed
	 * which determines with state it should be.
	 *
	 * If the recognizer has the state FAILED, CANCELLED or RECOGNIZED (equals ENDED), it is reset to
	 * POSSIBLE to give it another change on the next cycle.
	 *
	 *               Possible
	 *                  |
	 *            +-----+---------------+
	 *            |                     |
	 *      +-----+-----+               |
	 *      |           |               |
	 *   Failed      Cancelled          |
	 *                          +-------+------+
	 *                          |              |
	 *                      Recognized       Began
	 *                                         |
	 *                                      Changed
	 *                                         |
	 *                                  Ended/Recognized
	 */
	var STATE_POSSIBLE = 1;
	var STATE_BEGAN = 2;
	var STATE_CHANGED = 4;
	var STATE_ENDED = 8;
	var STATE_RECOGNIZED = STATE_ENDED;
	var STATE_CANCELLED = 16;
	var STATE_FAILED = 32;

	/**
	 * Recognizer
	 * Every recognizer needs to extend from this class.
	 * @constructor
	 * @param {Object} options
	 */
	function Recognizer(options) {
		this.id = uniqueId();

		this.manager = null;
		this.options = merge(options || {}, this.defaults);

		// default is enable true
		this.options.enable = ifUndefined(this.options.enable, true);

		this.state = STATE_POSSIBLE;

		this.simultaneous = {};
		this.requireFail = [];
	}

	Recognizer.prototype = {
		/**
		 * @virtual
		 * @type {Object}
		 */
		defaults: {},

		/**
		 * set options
		 * @param {Object} options
		 * @return {Recognizer}
		 */
		set: function (options) {
			extend(this.options, options);

			// also update the touchAction, in case something changed about the directions/enabled state
			this.manager && this.manager.touchAction.update();
			return this;
		},

		/**
		 * recognize simultaneous with an other recognizer.
		 * @param {Recognizer} otherRecognizer
		 * @returns {Recognizer} this
		 */
		recognizeWith: function (otherRecognizer) {
			if (invokeArrayArg(otherRecognizer, 'recognizeWith', this)) {
				return this;
			}

			var simultaneous = this.simultaneous;
			otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
			if (!simultaneous[otherRecognizer.id]) {
				simultaneous[otherRecognizer.id] = otherRecognizer;
				otherRecognizer.recognizeWith(this);
			}
			return this;
		},

		/**
		 * drop the simultaneous link. it doesnt remove the link on the other recognizer.
		 * @param {Recognizer} otherRecognizer
		 * @returns {Recognizer} this
		 */
		dropRecognizeWith: function (otherRecognizer) {
			if (invokeArrayArg(otherRecognizer, 'dropRecognizeWith', this)) {
				return this;
			}

			otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
			delete this.simultaneous[otherRecognizer.id];
			return this;
		},

		/**
		 * recognizer can only run when an other is failing
		 * @param {Recognizer} otherRecognizer
		 * @returns {Recognizer} this
		 */
		requireFailure: function (otherRecognizer) {
			if (invokeArrayArg(otherRecognizer, 'requireFailure', this)) {
				return this;
			}

			var requireFail = this.requireFail;
			otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
			if (inArray(requireFail, otherRecognizer) === -1) {
				requireFail.push(otherRecognizer);
				otherRecognizer.requireFailure(this);
			}
			return this;
		},

		/**
		 * drop the requireFailure link. it does not remove the link on the other recognizer.
		 * @param {Recognizer} otherRecognizer
		 * @returns {Recognizer} this
		 */
		dropRequireFailure: function (otherRecognizer) {
			if (invokeArrayArg(otherRecognizer, 'dropRequireFailure', this)) {
				return this;
			}

			otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
			var index = inArray(this.requireFail, otherRecognizer);
			if (index > -1) {
				this.requireFail.splice(index, 1);
			}
			return this;
		},

		/**
		 * has require failures boolean
		 * @returns {boolean}
		 */
		hasRequireFailures: function () {
			return this.requireFail.length > 0;
		},

		/**
		 * if the recognizer can recognize simultaneous with an other recognizer
		 * @param {Recognizer} otherRecognizer
		 * @returns {Boolean}
		 */
		canRecognizeWith: function (otherRecognizer) {
			return !!this.simultaneous[otherRecognizer.id];
		},

		/**
		 * You should use `tryEmit` instead of `emit` directly to check
		 * that all the needed recognizers has failed before emitting.
		 * @param {Object} input
		 */
		emit: function (input) {
			var self = this;
			var state = this.state;

			function emit(withState) {
				self.manager.emit(self.options.event + (withState ? stateStr(state) : ''), input);
			}

			// 'panstart' and 'panmove'
			if (state < STATE_ENDED) {
				emit(true);
			}

			emit(); // simple 'eventName' events

			// panend and pancancel
			if (state >= STATE_ENDED) {
				emit(true);
			}
		},

		/**
		 * Check that all the require failure recognizers has failed,
		 * if true, it emits a gesture event,
		 * otherwise, setup the state to FAILED.
		 * @param {Object} input
		 */
		tryEmit: function (input) {
			if (this.canEmit()) {
				return this.emit(input);
			}
			// it's failing anyway
			this.state = STATE_FAILED;
		},

		/**
		 * can we emit?
		 * @returns {boolean}
		 */
		canEmit: function () {
			var i = 0;
			while (i < this.requireFail.length) {
				if (!(this.requireFail[i].state & (STATE_FAILED | STATE_POSSIBLE))) {
					return false;
				}
				i++;
			}
			return true;
		},

		/**
		 * update the recognizer
		 * @param {Object} inputData
		 */
		recognize: function (inputData) {
			// make a new copy of the inputData
			// so we can change the inputData without messing up the other recognizers
			var inputDataClone = extend({}, inputData);

			// is is enabled and allow recognizing?
			if (!boolOrFn(this.options.enable, [this, inputDataClone])) {
				this.reset();
				this.state = STATE_FAILED;
				return;
			}

			// reset when we've reached the end
			if (this.state & (STATE_RECOGNIZED | STATE_CANCELLED | STATE_FAILED)) {
				this.state = STATE_POSSIBLE;
			}

			this.state = this.process(inputDataClone);

			// the recognizer has recognized a gesture
			// so trigger an event
			if (this.state & (STATE_BEGAN | STATE_CHANGED | STATE_ENDED | STATE_CANCELLED)) {
				this.tryEmit(inputDataClone);
			}
		},

		/**
		 * return the state of the recognizer
		 * the actual recognizing happens in this method
		 * @virtual
		 * @param {Object} inputData
		 * @returns {Const} STATE
		 */
		process: function (inputData) {
		}, // jshint ignore:line

		/**
		 * return the preferred touch-action
		 * @virtual
		 * @returns {Array}
		 */
		getTouchAction: function () {
		},

		/**
		 * called when the gesture isn't allowed to recognize
		 * like when another is being recognized or it is disabled
		 * @virtual
		 */
		reset: function () {
		}
	};

	/**
	 * get a usable string, used as event postfix
	 * @param {Const} state
	 * @returns {String} state
	 */
	function stateStr(state) {
		if (state & STATE_CANCELLED) {
			return 'cancel';
		} else if (state & STATE_ENDED) {
			return 'end';
		} else if (state & STATE_CHANGED) {
			return 'move';
		} else if (state & STATE_BEGAN) {
			return 'start';
		}
		return '';
	}

	/**
	 * direction cons to string
	 * @param {Const} direction
	 * @returns {String}
	 */
	function directionStr(direction) {
		if (direction == DIRECTION_DOWN) {
			return 'down';
		} else if (direction == DIRECTION_UP) {
			return 'up';
		} else if (direction == DIRECTION_LEFT) {
			return 'left';
		} else if (direction == DIRECTION_RIGHT) {
			return 'right';
		}
		return '';
	}

	/**
	 * get a recognizer by name if it is bound to a manager
	 * @param {Recognizer|String} otherRecognizer
	 * @param {Recognizer} recognizer
	 * @returns {Recognizer}
	 */
	function getRecognizerByNameIfManager(otherRecognizer, recognizer) {
		var manager = recognizer.manager;
		if (manager) {
			return manager.get(otherRecognizer);
		}
		return otherRecognizer;
	}

	/**
	 * This recognizer is just used as a base for the simple attribute recognizers.
	 * @constructor
	 * @extends Recognizer
	 */
	function AttrRecognizer() {
		Recognizer.apply(this, arguments);
	}

	inherit(AttrRecognizer, Recognizer, {
		/**
		 * @namespace
		 * @memberof AttrRecognizer
		 */
		defaults: {
			/**
			 * @type {Number}
			 * @default 1
			 */
			pointers: 1
		},

		/**
		 * Used to check if it the recognizer receives valid input, like input.distance > 10.
		 * @memberof AttrRecognizer
		 * @param {Object} input
		 * @returns {Boolean} recognized
		 */
		attrTest: function (input) {
			var optionPointers = this.options.pointers;
			return optionPointers === 0 || input.pointers.length === optionPointers;
		},

		/**
		 * Process the input and return the state for the recognizer
		 * @memberof AttrRecognizer
		 * @param {Object} input
		 * @returns {*} State
		 */
		process: function (input) {
			var state = this.state;
			var eventType = input.eventType;

			var isRecognized = state & (STATE_BEGAN | STATE_CHANGED);
			var isValid = this.attrTest(input);

			// on cancel input and we've recognized before, return STATE_CANCELLED
			if (isRecognized && (eventType & INPUT_CANCEL || !isValid)) {
				return state | STATE_CANCELLED;
			} else if (isRecognized || isValid) {
				if (eventType & INPUT_END) {
					return state | STATE_ENDED;
				} else if (!(state & STATE_BEGAN)) {
					return STATE_BEGAN;
				}
				return state | STATE_CHANGED;
			}
			return STATE_FAILED;
		}
	});

	/**
	 * Pan
	 * Recognized when the pointer is down and moved in the allowed direction.
	 * @constructor
	 * @extends AttrRecognizer
	 */
	function PanRecognizer() {
		AttrRecognizer.apply(this, arguments);

		this.pX = null;
		this.pY = null;
	}

	inherit(PanRecognizer, AttrRecognizer, {
		/**
		 * @namespace
		 * @memberof PanRecognizer
		 */
		defaults: {
			event: 'pan',
			threshold: 10,
			pointers: 1,
			direction: DIRECTION_ALL
		},

		getTouchAction: function () {
			var direction = this.options.direction;
			var actions = [];
			if (direction & DIRECTION_HORIZONTAL) {
				actions.push(TOUCH_ACTION_PAN_Y);
			}
			if (direction & DIRECTION_VERTICAL) {
				actions.push(TOUCH_ACTION_PAN_X);
			}
			return actions;
		},

		directionTest: function (input) {
			var options = this.options;
			var hasMoved = true;
			var distance = input.distance;
			var direction = input.direction;
			var x = input.deltaX;
			var y = input.deltaY;

			// lock to axis?
			if (!(direction & options.direction)) {
				if (options.direction & DIRECTION_HORIZONTAL) {
					direction = (x === 0) ? DIRECTION_NONE : (x < 0) ? DIRECTION_LEFT : DIRECTION_RIGHT;
					hasMoved = x != this.pX;
					distance = Math.abs(input.deltaX);
				} else {
					direction = (y === 0) ? DIRECTION_NONE : (y < 0) ? DIRECTION_UP : DIRECTION_DOWN;
					hasMoved = y != this.pY;
					distance = Math.abs(input.deltaY);
				}
			}
			input.direction = direction;
			return hasMoved && distance > options.threshold && direction & options.direction;
		},

		attrTest: function (input) {
			return AttrRecognizer.prototype.attrTest.call(this, input) &&
				(this.state & STATE_BEGAN || (!(this.state & STATE_BEGAN) && this.directionTest(input)));
		},

		emit: function (input) {
			this.pX = input.deltaX;
			this.pY = input.deltaY;

			var direction = directionStr(input.direction);
			if (direction) {
				this.manager.emit(this.options.event + direction, input);
			}

			this._super.emit.call(this, input);
		}
	});

	/**
	 * Pinch
	 * Recognized when two or more pointers are moving toward (zoom-in) or away from each other (zoom-out).
	 * @constructor
	 * @extends AttrRecognizer
	 */
	function PinchRecognizer() {
		AttrRecognizer.apply(this, arguments);
	}

	inherit(PinchRecognizer, AttrRecognizer, {
		/**
		 * @namespace
		 * @memberof PinchRecognizer
		 */
		defaults: {
			event: 'pinch',
			threshold: 0,
			pointers: 2
		},

		getTouchAction: function () {
			return [TOUCH_ACTION_NONE];
		},

		attrTest: function (input) {
			return this._super.attrTest.call(this, input) &&
				(Math.abs(input.scale - 1) > this.options.threshold || this.state & STATE_BEGAN);
		},

		emit: function (input) {
			this._super.emit.call(this, input);
			if (input.scale !== 1) {
				var inOut = input.scale < 1 ? 'in' : 'out';
				this.manager.emit(this.options.event + inOut, input);
			}
		}
	});

	/**
	 * Press
	 * Recognized when the pointer is down for x ms without any movement.
	 * @constructor
	 * @extends Recognizer
	 */
	function PressRecognizer() {
		Recognizer.apply(this, arguments);

		this._timer = null;
		this._input = null;
	}

	inherit(PressRecognizer, Recognizer, {
		/**
		 * @namespace
		 * @memberof PressRecognizer
		 */
		defaults: {
			event: 'press',
			pointers: 1,
			time: 500, // minimal time of the pointer to be pressed
			threshold: 5 // a minimal movement is ok, but keep it low
		},

		getTouchAction: function () {
			return [TOUCH_ACTION_AUTO];
		},

		process: function (input) {
			var options = this.options;
			var validPointers = input.pointers.length === options.pointers;
			var validMovement = input.distance < options.threshold;
			var validTime = input.deltaTime > options.time;

			this._input = input;

			// we only allow little movement
			// and we've reached an end event, so a tap is possible
			if (!validMovement || !validPointers || (input.eventType & (INPUT_END | INPUT_CANCEL) && !validTime)) {
				this.reset();
			} else if (input.eventType & INPUT_START) {
				this.reset();
				this._timer = setTimeoutContext(function () {
					this.state = STATE_RECOGNIZED;
					this.tryEmit();
				}, options.time, this);
			} else if (input.eventType & INPUT_END) {
				return STATE_RECOGNIZED;
			}
			return STATE_FAILED;
		},

		reset: function () {
			clearTimeout(this._timer);
		},

		emit: function (input) {
			if (this.state !== STATE_RECOGNIZED) {
				return;
			}

			if (input && (input.eventType & INPUT_END)) {
				this.manager.emit(this.options.event + 'up', input);
			} else {
				this._input.timeStamp = now();
				this.manager.emit(this.options.event, this._input);
			}
		}
	});

	/**
	 * Rotate
	 * Recognized when two or more pointer are moving in a circular motion.
	 * @constructor
	 * @extends AttrRecognizer
	 */
	function RotateRecognizer() {
		AttrRecognizer.apply(this, arguments);
	}

	inherit(RotateRecognizer, AttrRecognizer, {
		/**
		 * @namespace
		 * @memberof RotateRecognizer
		 */
		defaults: {
			event: 'rotate',
			threshold: 0,
			pointers: 2
		},

		getTouchAction: function () {
			return [TOUCH_ACTION_NONE];
		},

		attrTest: function (input) {
			return this._super.attrTest.call(this, input) &&
				(Math.abs(input.rotation) > this.options.threshold || this.state & STATE_BEGAN);
		}
	});

	/**
	 * Swipe
	 * Recognized when the pointer is moving fast (velocity), with enough distance in the allowed direction.
	 * @constructor
	 * @extends AttrRecognizer
	 */
	function SwipeRecognizer() {
		AttrRecognizer.apply(this, arguments);
	}

	inherit(SwipeRecognizer, AttrRecognizer, {
		/**
		 * @namespace
		 * @memberof SwipeRecognizer
		 */
		defaults: {
			event: 'swipe',
			threshold: 10,
			velocity: 0.65,
			direction: DIRECTION_HORIZONTAL | DIRECTION_VERTICAL,
			pointers: 1
		},

		getTouchAction: function () {
			return PanRecognizer.prototype.getTouchAction.call(this);
		},

		attrTest: function (input) {
			var direction = this.options.direction;
			var velocity;

			if (direction & (DIRECTION_HORIZONTAL | DIRECTION_VERTICAL)) {
				velocity = input.velocity;
			} else if (direction & DIRECTION_HORIZONTAL) {
				velocity = input.velocityX;
			} else if (direction & DIRECTION_VERTICAL) {
				velocity = input.velocityY;
			}

			return this._super.attrTest.call(this, input) &&
				direction & input.direction &&
				input.distance > this.options.threshold &&
				abs(velocity) > this.options.velocity && input.eventType & INPUT_END;
		},

		emit: function (input) {
			var direction = directionStr(input.direction);
			if (direction) {
				this.manager.emit(this.options.event + direction, input);
			}

			this.manager.emit(this.options.event, input);
		}
	});

	/**
	 * A tap is ecognized when the pointer is doing a small tap/click. Multiple taps are recognized if they occur
	 * between the given interval and position. The delay option can be used to recognize multi-taps without firing
	 * a single tap.
	 *
	 * The eventData from the emitted event contains the property `tapCount`, which contains the amount of
	 * multi-taps being recognized.
	 * @constructor
	 * @extends Recognizer
	 */
	function TapRecognizer() {
		Recognizer.apply(this, arguments);

		// previous time and center,
		// used for tap counting
		this.pTime = false;
		this.pCenter = false;

		this._timer = null;
		this._input = null;
		this.count = 0;
	}

	inherit(TapRecognizer, Recognizer, {
		/**
		 * @namespace
		 * @memberof PinchRecognizer
		 */
		defaults: {
			event: 'tap',
			pointers: 1,
			taps: 1,
			interval: 300, // max time between the multi-tap taps
			time: 250, // max time of the pointer to be down (like finger on the screen)
			threshold: 2, // a minimal movement is ok, but keep it low
			posThreshold: 10 // a multi-tap can be a bit off the initial position
		},

		getTouchAction: function () {
			return [TOUCH_ACTION_MANIPULATION];
		},

		process: function (input) {
			var options = this.options;

			var validPointers = input.pointers.length === options.pointers;
			var validMovement = input.distance < options.threshold;
			var validTouchTime = input.deltaTime < options.time;

			this.reset();

			if ((input.eventType & INPUT_START) && (this.count === 0)) {
				return this.failTimeout();
			}

			// we only allow little movement
			// and we've reached an end event, so a tap is possible
			if (validMovement && validTouchTime && validPointers) {
				if (input.eventType != INPUT_END) {
					return this.failTimeout();
				}

				var validInterval = this.pTime ? (input.timeStamp - this.pTime < options.interval) : true;
				var validMultiTap = !this.pCenter || getDistance(this.pCenter, input.center) < options.posThreshold;

				this.pTime = input.timeStamp;
				this.pCenter = input.center;

				if (!validMultiTap || !validInterval) {
					this.count = 1;
				} else {
					this.count += 1;
				}

				this._input = input;

				// if tap count matches we have recognized it,
				// else it has began recognizing...
				var tapCount = this.count % options.taps;
				if (tapCount === 0) {
					// no failing requirements, immediately trigger the tap event
					// or wait as long as the multitap interval to trigger
					if (!this.hasRequireFailures()) {
						return STATE_RECOGNIZED;
					} else {
						this._timer = setTimeoutContext(function () {
							this.state = STATE_RECOGNIZED;
							this.tryEmit();
						}, options.interval, this);
						return STATE_BEGAN;
					}
				}
			}
			return STATE_FAILED;
		},

		failTimeout: function () {
			this._timer = setTimeoutContext(function () {
				this.state = STATE_FAILED;
			}, this.options.interval, this);
			return STATE_FAILED;
		},

		reset: function () {
			clearTimeout(this._timer);
		},

		emit: function () {
			if (this.state == STATE_RECOGNIZED) {
				this._input.tapCount = this.count;
				this.manager.emit(this.options.event, this._input);
			}
		}
	});

	/**
	 * Simple way to create an manager with a default set of recognizers.
	 * @param {HTMLElement} element
	 * @param {Object} [options]
	 * @constructor
	 */
	function Hammer(element, options) {
		options = options || {};
		options.recognizers = ifUndefined(options.recognizers, Hammer.defaults.preset);
		return new Manager(element, options);
	}

	/**
	 * @const {string}
	 */
	Hammer.VERSION = '2.0.4';

	/**
	 * default settings
	 * @namespace
	 */
	Hammer.defaults = {
		/**
		 * set if DOM events are being triggered.
		 * But this is slower and unused by simple implementations, so disabled by default.
		 * @type {Boolean}
		 * @default false
		 */
		domEvents: false,

		/**
		 * The value for the touchAction property/fallback.
		 * When set to `compute` it will magically set the correct value based on the added recognizers.
		 * @type {String}
		 * @default compute
		 */
		touchAction: TOUCH_ACTION_COMPUTE,

		/**
		 * @type {Boolean}
		 * @default true
		 */
		enable: true,

		/**
		 * EXPERIMENTAL FEATURE -- can be removed/changed
		 * Change the parent input target element.
		 * If Null, then it is being set the to main element.
		 * @type {Null|EventTarget}
		 * @default null
		 */
		inputTarget: null,

		/**
		 * force an input class
		 * @type {Null|Function}
		 * @default null
		 */
		inputClass: null,

		/**
		 * Default recognizer setup when calling `Hammer()`
		 * When creating a new Manager these will be skipped.
		 * @type {Array}
		 */
		preset: [
			// RecognizerClass, options, [recognizeWith, ...], [requireFailure, ...]
			[RotateRecognizer, { enable: false }],
			[PinchRecognizer, { enable: false }, ['rotate']],
			[SwipeRecognizer, { direction: DIRECTION_HORIZONTAL }],
			[PanRecognizer, { direction: DIRECTION_HORIZONTAL }, ['swipe']],
			[TapRecognizer],
			[TapRecognizer, { event: 'doubletap', taps: 2 }, ['tap']],
			[PressRecognizer]
		],

		/**
		 * Some CSS properties can be used to improve the working of Hammer.
		 * Add them to this method and they will be set when creating a new Manager.
		 * @namespace
		 */
		cssProps: {
			/**
			 * Disables text selection to improve the dragging gesture. Mainly for desktop browsers.
			 * @type {String}
			 * @default 'none'
			 */
			userSelect: 'none',

			/**
			 * Disable the Windows Phone grippers when pressing an element.
			 * @type {String}
			 * @default 'none'
			 */
			touchSelect: 'none',

			/**
			 * Disables the default callout shown when you touch and hold a touch target.
			 * On iOS, when you touch and hold a touch target such as a link, Safari displays
			 * a callout containing information about the link. This property allows you to disable that callout.
			 * @type {String}
			 * @default 'none'
			 */
			touchCallout: 'none',

			/**
			 * Specifies whether zooming is enabled. Used by IE10>
			 * @type {String}
			 * @default 'none'
			 */
			contentZooming: 'none',

			/**
			 * Specifies that an entire element should be draggable instead of its contents. Mainly for desktop browsers.
			 * @type {String}
			 * @default 'none'
			 */
			userDrag: 'none',

			/**
			 * Overrides the highlight color shown when the user taps a link or a JavaScript
			 * clickable element in iOS. This property obeys the alpha value, if specified.
			 * @type {String}
			 * @default 'rgba(0,0,0,0)'
			 */
			tapHighlightColor: 'rgba(0,0,0,0)'
		}
	};

	var STOP = 1;
	var FORCED_STOP = 2;

	/**
	 * Manager
	 * @param {HTMLElement} element
	 * @param {Object} [options]
	 * @constructor
	 */
	function Manager(element, options) {
		options = options || {};

		this.options = merge(options, Hammer.defaults);
		this.options.inputTarget = this.options.inputTarget || element;

		this.handlers = {};
		this.session = {};
		this.recognizers = [];

		this.element = element;
		this.input = createInputInstance(this);
		this.touchAction = new TouchAction(this, this.options.touchAction);

		toggleCssProps(this, true);

		each(options.recognizers, function (item) {
			var recognizer = this.add(new (item[0])(item[1]));
			item[2] && recognizer.recognizeWith(item[2]);
			item[3] && recognizer.requireFailure(item[3]);
		}, this);
	}

	Manager.prototype = {
		/**
		 * set options
		 * @param {Object} options
		 * @returns {Manager}
		 */
		set: function (options) {
			extend(this.options, options);

			// Options that need a little more setup
			if (options.touchAction) {
				this.touchAction.update();
			}
			if (options.inputTarget) {
				// Clean up existing event listeners and reinitialize
				this.input.destroy();
				this.input.target = options.inputTarget;
				this.input.init();
			}
			return this;
		},

		/**
		 * stop recognizing for this session.
		 * This session will be discarded, when a new [input]start event is fired.
		 * When forced, the recognizer cycle is stopped immediately.
		 * @param {Boolean} [force]
		 */
		stop: function (force) {
			this.session.stopped = force ? FORCED_STOP : STOP;
		},

		/**
		 * run the recognizers!
		 * called by the inputHandler function on every movement of the pointers (touches)
		 * it walks through all the recognizers and tries to detect the gesture that is being made
		 * @param {Object} inputData
		 */
		recognize: function (inputData) {
			var session = this.session;
			if (session.stopped) {
				return;
			}

			// run the touch-action polyfill
			this.touchAction.preventDefaults(inputData);

			var recognizer;
			var recognizers = this.recognizers;

			// this holds the recognizer that is being recognized.
			// so the recognizer's state needs to be BEGAN, CHANGED, ENDED or RECOGNIZED
			// if no recognizer is detecting a thing, it is set to `null`
			var curRecognizer = session.curRecognizer;

			// reset when the last recognizer is recognized
			// or when we're in a new session
			if (!curRecognizer || (curRecognizer && curRecognizer.state & STATE_RECOGNIZED)) {
				curRecognizer = session.curRecognizer = null;
			}

			var i = 0;
			while (i < recognizers.length) {
				recognizer = recognizers[i];

				// find out if we are allowed try to recognize the input for this one.
				// 1.   allow if the session is NOT forced stopped (see the .stop() method)
				// 2.   allow if we still haven't recognized a gesture in this session, or the this recognizer is the one
				//      that is being recognized.
				// 3.   allow if the recognizer is allowed to run simultaneous with the current recognized recognizer.
				//      this can be setup with the `recognizeWith()` method on the recognizer.
				if (session.stopped !== FORCED_STOP && ( // 1
					!curRecognizer || recognizer == curRecognizer || // 2
						recognizer.canRecognizeWith(curRecognizer))) { // 3
					recognizer.recognize(inputData);
				} else {
					recognizer.reset();
				}

				// if the recognizer has been recognizing the input as a valid gesture, we want to store this one as the
				// current active recognizer. but only if we don't already have an active recognizer
				if (!curRecognizer && recognizer.state & (STATE_BEGAN | STATE_CHANGED | STATE_ENDED)) {
					curRecognizer = session.curRecognizer = recognizer;
				}
				i++;
			}
		},

		/**
		 * get a recognizer by its event name.
		 * @param {Recognizer|String} recognizer
		 * @returns {Recognizer|Null}
		 */
		get: function (recognizer) {
			if (recognizer instanceof Recognizer) {
				return recognizer;
			}

			var recognizers = this.recognizers;
			for (var i = 0; i < recognizers.length; i++) {
				if (recognizers[i].options.event == recognizer) {
					return recognizers[i];
				}
			}
			return null;
		},

		/**
		 * add a recognizer to the manager
		 * existing recognizers with the same event name will be removed
		 * @param {Recognizer} recognizer
		 * @returns {Recognizer|Manager}
		 */
		add: function (recognizer) {
			if (invokeArrayArg(recognizer, 'add', this)) {
				return this;
			}

			// remove existing
			var existing = this.get(recognizer.options.event);
			if (existing) {
				this.remove(existing);
			}

			this.recognizers.push(recognizer);
			recognizer.manager = this;

			this.touchAction.update();
			return recognizer;
		},

		/**
		 * remove a recognizer by name or instance
		 * @param {Recognizer|String} recognizer
		 * @returns {Manager}
		 */
		remove: function (recognizer) {
			if (invokeArrayArg(recognizer, 'remove', this)) {
				return this;
			}

			var recognizers = this.recognizers;
			recognizer = this.get(recognizer);
			recognizers.splice(inArray(recognizers, recognizer), 1);

			this.touchAction.update();
			return this;
		},

		/**
		 * bind event
		 * @param {String} events
		 * @param {Function} handler
		 * @returns {EventEmitter} this
		 */
		on: function (events, handler) {
			var handlers = this.handlers;
			each(splitStr(events), function (event) {
				handlers[event] = handlers[event] || [];
				handlers[event].push(handler);
			});
			return this;
		},

		/**
		 * unbind event, leave emit blank to remove all handlers
		 * @param {String} events
		 * @param {Function} [handler]
		 * @returns {EventEmitter} this
		 */
		off: function (events, handler) {
			var handlers = this.handlers;
			each(splitStr(events), function (event) {
				if (!handler) {
					delete handlers[event];
				} else {
					handlers[event].splice(inArray(handlers[event], handler), 1);
				}
			});
			return this;
		},

		/**
		 * emit event to the listeners
		 * @param {String} event
		 * @param {Object} data
		 */
		emit: function (event, data) {
			// we also want to trigger dom events
			if (this.options.domEvents) {
				triggerDomEvent(event, data);
			}

			// no handlers, so skip it all
			var handlers = this.handlers[event] && this.handlers[event].slice();
			if (!handlers || !handlers.length) {
				return;
			}

			data.type = event;
			data.preventDefault = function () {
				data.srcEvent.preventDefault();
			};

			var i = 0;
			while (i < handlers.length) {
				handlers[i](data);
				i++;
			}
		},

		/**
		 * destroy the manager and unbinds all events
		 * it doesn't unbind dom events, that is the user own responsibility
		 */
		destroy: function () {
			this.element && toggleCssProps(this, false);

			this.handlers = {};
			this.session = {};
			this.input.destroy();
			this.element = null;
		}
	};

	/**
	 * add/remove the css properties as defined in manager.options.cssProps
	 * @param {Manager} manager
	 * @param {Boolean} add
	 */
	function toggleCssProps(manager, add) {
		var element = manager.element;
		each(manager.options.cssProps, function (value, name) {
			element.style[prefixed(element.style, name)] = add ? value : '';
		});
	}

	/**
	 * trigger dom event
	 * @param {String} event
	 * @param {Object} data
	 */
	function triggerDomEvent(event, data) {
		var gestureEvent = document.createEvent('Event');
		gestureEvent.initEvent(event, true, true);
		gestureEvent.gesture = data;
		data.target.dispatchEvent(gestureEvent);
	}

	extend(Hammer, {
		INPUT_START: INPUT_START,
		INPUT_MOVE: INPUT_MOVE,
		INPUT_END: INPUT_END,
		INPUT_CANCEL: INPUT_CANCEL,

		STATE_POSSIBLE: STATE_POSSIBLE,
		STATE_BEGAN: STATE_BEGAN,
		STATE_CHANGED: STATE_CHANGED,
		STATE_ENDED: STATE_ENDED,
		STATE_RECOGNIZED: STATE_RECOGNIZED,
		STATE_CANCELLED: STATE_CANCELLED,
		STATE_FAILED: STATE_FAILED,

		DIRECTION_NONE: DIRECTION_NONE,
		DIRECTION_LEFT: DIRECTION_LEFT,
		DIRECTION_RIGHT: DIRECTION_RIGHT,
		DIRECTION_UP: DIRECTION_UP,
		DIRECTION_DOWN: DIRECTION_DOWN,
		DIRECTION_HORIZONTAL: DIRECTION_HORIZONTAL,
		DIRECTION_VERTICAL: DIRECTION_VERTICAL,
		DIRECTION_ALL: DIRECTION_ALL,

		Manager: Manager,
		Input: Input,
		TouchAction: TouchAction,

		TouchInput: TouchInput,
		MouseInput: MouseInput,
		PointerEventInput: PointerEventInput,
		TouchMouseInput: TouchMouseInput,
		SingleTouchInput: SingleTouchInput,

		Recognizer: Recognizer,
		AttrRecognizer: AttrRecognizer,
		Tap: TapRecognizer,
		Pan: PanRecognizer,
		Swipe: SwipeRecognizer,
		Pinch: PinchRecognizer,
		Rotate: RotateRecognizer,
		Press: PressRecognizer,

		on: addEventListeners,
		off: removeEventListeners,
		each: each,
		merge: merge,
		extend: extend,
		inherit: inherit,
		bindFn: bindFn,
		prefixed: prefixed
	});

	if (typeof define == TYPE_FUNCTION && define.amd) {
		define(function () {
			return Hammer;
		});
	} else if (typeof module != 'undefined' && module.exports) {
		module.exports = Hammer;
	} else {
		window[exportName] = Hammer;
	}

})(window, document, 'Hammer');
(function (window, document, Math) {
var rAF = window.requestAnimationFrame	||
	window.webkitRequestAnimationFrame	||
	window.mozRequestAnimationFrame		||
	window.oRequestAnimationFrame		||
	window.msRequestAnimationFrame		||
	function (callback) { window.setTimeout(callback, 1000 / 60); };

var utils = (function () {
	var me = {};

	var _elementStyle = document.createElement('div').style;
	var _vendor = (function () {
		var vendors = ['t', 'webkitT', 'MozT', 'msT', 'OT'],
			transform,
			i = 0,
			l = vendors.length;

		for ( ; i < l; i++ ) {
			transform = vendors[i] + 'ransform';
			if ( transform in _elementStyle ) return vendors[i].substr(0, vendors[i].length-1);
		}

		return false;
	})();

	function _prefixStyle (style) {
		if ( _vendor === false ) return false;
		if ( _vendor === '' ) return style;
		return _vendor + style.charAt(0).toUpperCase() + style.substr(1);
	}

	me.getTime = Date.now || function getTime () { return new Date().getTime(); };

	me.extend = function (target, obj) {
		for ( var i in obj ) {
			target[i] = obj[i];
		}
	};

	me.addEvent = function (el, type, fn, capture) {
		el.addEventListener(type, fn, !!capture);
	};

	me.removeEvent = function (el, type, fn, capture) {
		el.removeEventListener(type, fn, !!capture);
	};

	me.prefixPointerEvent = function (pointerEvent) {
		return window.MSPointerEvent ?
			'MSPointer' + pointerEvent.charAt(9).toUpperCase() + pointerEvent.substr(10):
			pointerEvent;
	};

	me.momentum = function (current, start, time, lowerMargin, wrapperSize, deceleration) {
		var distance = current - start,
			speed = Math.abs(distance) / time,
			destination,
			duration;

		deceleration = deceleration === undefined ? 0.0006 : deceleration;

		destination = current + ( speed * speed ) / ( 2 * deceleration ) * ( distance < 0 ? -1 : 1 );
		duration = speed / deceleration;

		if ( destination < lowerMargin ) {
			destination = wrapperSize ? lowerMargin - ( wrapperSize / 2.5 * ( speed / 8 ) ) : lowerMargin;
			distance = Math.abs(destination - current);
			duration = distance / speed;
		} else if ( destination > 0 ) {
			destination = wrapperSize ? wrapperSize / 2.5 * ( speed / 8 ) : 0;
			distance = Math.abs(current) + destination;
			duration = distance / speed;
		}

		return {
			destination: Math.round(destination),
			duration: duration
		};
	};

	var _transform = _prefixStyle('transform');

	me.extend(me, {
		hasTransform: _transform !== false,
		hasPerspective: _prefixStyle('perspective') in _elementStyle,
		hasTouch: 'ontouchstart' in window,
		hasPointer: window.PointerEvent || window.MSPointerEvent, // IE10 is prefixed
		hasTransition: _prefixStyle('transition') in _elementStyle
	});

	// This should find all Android browsers lower than build 535.19 (both stock browser and webview)
	me.isBadAndroid = /Android /.test(window.navigator.appVersion) && !(/Chrome\/\d/.test(window.navigator.appVersion));

	me.extend(me.style = {}, {
		transform: _transform,
		transitionTimingFunction: _prefixStyle('transitionTimingFunction'),
		transitionDuration: _prefixStyle('transitionDuration'),
		transitionDelay: _prefixStyle('transitionDelay'),
		transformOrigin: _prefixStyle('transformOrigin')
	});

	me.hasClass = function (e, c) {
		var re = new RegExp("(^|\\s)" + c + "(\\s|$)");
		return re.test(e.className);
	};

	me.addClass = function (e, c) {
		if ( me.hasClass(e, c) ) {
			return;
		}

		var newclass = e.className.split(' ');
		newclass.push(c);
		e.className = newclass.join(' ');
	};

	me.removeClass = function (e, c) {
		if ( !me.hasClass(e, c) ) {
			return;
		}

		var re = new RegExp("(^|\\s)" + c + "(\\s|$)", 'g');
		e.className = e.className.replace(re, ' ');
	};

	me.offset = function (el) {
		var left = -el.offsetLeft,
			top = -el.offsetTop;

		// jshint -W084
		while (el = el.offsetParent) {
			left -= el.offsetLeft;
			top -= el.offsetTop;
		}
		// jshint +W084

		return {
			left: left,
			top: top
		};
	};

	me.preventDefaultException = function (el, exceptions) {
		for ( var i in exceptions ) {
			if ( exceptions[i].test(el[i]) ) {
				return true;
			}
		}

		return false;
	};

	me.extend(me.eventType = {}, {
		touchstart: 1,
		touchmove: 1,
		touchend: 1,

		mousedown: 2,
		mousemove: 2,
		mouseup: 2,

		pointerdown: 3,
		pointermove: 3,
		pointerup: 3,

		MSPointerDown: 3,
		MSPointerMove: 3,
		MSPointerUp: 3
	});

	me.extend(me.ease = {}, {
		quadratic: {
			style: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
			fn: function (k) {
				return k * ( 2 - k );
			}
		},
		circular: {
			style: 'cubic-bezier(0.1, 0.57, 0.1, 1)',	// Not properly "circular" but this looks better, it should be (0.075, 0.82, 0.165, 1)
			fn: function (k) {
				return Math.sqrt( 1 - ( --k * k ) );
			}
		},
		back: {
			style: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
			fn: function (k) {
				var b = 4;
				return ( k = k - 1 ) * k * ( ( b + 1 ) * k + b ) + 1;
			}
		},
		bounce: {
			style: '',
			fn: function (k) {
				if ( ( k /= 1 ) < ( 1 / 2.75 ) ) {
					return 7.5625 * k * k;
				} else if ( k < ( 2 / 2.75 ) ) {
					return 7.5625 * ( k -= ( 1.5 / 2.75 ) ) * k + 0.75;
				} else if ( k < ( 2.5 / 2.75 ) ) {
					return 7.5625 * ( k -= ( 2.25 / 2.75 ) ) * k + 0.9375;
				} else {
					return 7.5625 * ( k -= ( 2.625 / 2.75 ) ) * k + 0.984375;
				}
			}
		},
		elastic: {
			style: '',
			fn: function (k) {
				var f = 0.22,
					e = 0.4;

				if ( k === 0 ) { return 0; }
				if ( k == 1 ) { return 1; }

				return ( e * Math.pow( 2, - 10 * k ) * Math.sin( ( k - f / 4 ) * ( 2 * Math.PI ) / f ) + 1 );
			}
		}
	});

	me.tap = function (e, eventName) {
		var ev = document.createEvent('Event');
		ev.initEvent(eventName, true, true);
		ev.pageX = e.pageX;
		ev.pageY = e.pageY;
		e.target.dispatchEvent(ev);
	};

	me.click = function (e) {
		var target = e.target,
			ev;

		if ( !(/(SELECT|INPUT|TEXTAREA)/i).test(target.tagName) ) {
			ev = document.createEvent('MouseEvents');
			ev.initMouseEvent('click', true, true, e.view, 1,
				target.screenX, target.screenY, target.clientX, target.clientY,
				e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
				0, null);

			ev._constructed = true;
			target.dispatchEvent(ev);
		}
	};

	return me;
})();

function IScroll (el, options) {
	this.wrapper = typeof el == 'string' ? document.querySelector(el) : el;
	this.scroller = this.wrapper.children[0];
	this.scrollerStyle = this.scroller.style;		// cache style for better performance

	this.options = {

		zoomMin: 1,
		zoomMax: 4, zoomStart: 1,

		resizeScrollbars: true,

		mouseWheelSpeed: 20,

		snapThreshold: 0.334,

// INSERT POINT: OPTIONS

		startX: 0,
		startY: 0,
		scrollY: true,
		directionLockThreshold: 5,
		momentum: true,

		bounce: true,
		bounceTime: 600,
		bounceEasing: '',

		preventDefault: true,
		preventDefaultException: { tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/ },

		HWCompositing: true,
		useTransition: true,
		useTransform: true
	};

	for ( var i in options ) {
		this.options[i] = options[i];
	}

	// Normalize options
	this.translateZ = this.options.HWCompositing && utils.hasPerspective ? ' translateZ(0)' : '';

	this.options.useTransition = utils.hasTransition && this.options.useTransition;
	this.options.useTransform = utils.hasTransform && this.options.useTransform;

	this.options.eventPassthrough = this.options.eventPassthrough === true ? 'vertical' : this.options.eventPassthrough;
	this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault;

	// If you want eventPassthrough I have to lock one of the axes
	this.options.scrollY = this.options.eventPassthrough == 'vertical' ? false : this.options.scrollY;
	this.options.scrollX = this.options.eventPassthrough == 'horizontal' ? false : this.options.scrollX;

	// With eventPassthrough we also need lockDirection mechanism
	this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough;
	this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold;

	this.options.bounceEasing = typeof this.options.bounceEasing == 'string' ? utils.ease[this.options.bounceEasing] || utils.ease.circular : this.options.bounceEasing;

	this.options.resizePolling = this.options.resizePolling === undefined ? 60 : this.options.resizePolling;

	if ( this.options.tap === true ) {
		this.options.tap = 'tap';
	}

	if ( this.options.shrinkScrollbars == 'scale' ) {
		this.options.useTransition = false;
	}

	this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1;

// INSERT POINT: NORMALIZATION

	// Some defaults
	this.x = 0;
	this.y = 0;
	this.directionX = 0;
	this.directionY = 0;
	this._events = {};

	this.scale = Math.min(Math.max(this.options.zoomStart, this.options.zoomMin), this.options.zoomMax);

// INSERT POINT: DEFAULTS

	this._init();
	this.refresh();

	this.scrollTo(this.options.startX, this.options.startY);
	this.enable();
}

IScroll.prototype = {
	version: '5.1.3',

	_init: function () {
		this._initEvents();

		if ( this.options.zoom ) {
			this._initZoom();
		}

		if ( this.options.scrollbars || this.options.indicators ) {
			this._initIndicators();
		}

		if ( this.options.mouseWheel ) {
			this._initWheel();
		}

		if ( this.options.snap ) {
			this._initSnap();
		}

		if ( this.options.keyBindings ) {
			this._initKeys();
		}

// INSERT POINT: _init

	},

	destroy: function () {
		this._initEvents(true);

		this._execEvent('destroy');
	},

	_transitionEnd: function (e) {
		if ( e.target != this.scroller || !this.isInTransition ) {
			return;
		}

		this._transitionTime();
		if ( !this.resetPosition(this.options.bounceTime) ) {
			this.isInTransition = false;
			this._execEvent('scrollEnd');
		}
	},

	_start: function (e) {
		// React to left mouse button only
		if ( utils.eventType[e.type] != 1 ) {
			if ( e.button !== 0 ) {
				return;
			}
		}

		if ( !this.enabled || (this.initiated && utils.eventType[e.type] !== this.initiated) ) {
			return;
		}

		if ( this.options.preventDefault && !utils.isBadAndroid && !utils.preventDefaultException(e.target, this.options.preventDefaultException) ) {
			e.preventDefault();
		}

		var point = e.touches ? e.touches[0] : e,
			pos;

		this.initiated	= utils.eventType[e.type];
		this.moved		= false;
		this.distX		= 0;
		this.distY		= 0;
		this.directionX = 0;
		this.directionY = 0;
		this.directionLocked = 0;

		this._transitionTime();

		this.startTime = utils.getTime();

		if ( this.options.useTransition && this.isInTransition ) {
			this.isInTransition = false;
			pos = this.getComputedPosition();
			this._translate(Math.round(pos.x), Math.round(pos.y));
			this._execEvent('scrollEnd');
		} else if ( !this.options.useTransition && this.isAnimating ) {
			this.isAnimating = false;
			this._execEvent('scrollEnd');
		}

		this.startX    = this.x;
		this.startY    = this.y;
		this.absStartX = this.x;
		this.absStartY = this.y;
		this.pointX    = point.pageX;
		this.pointY    = point.pageY;

		this._execEvent('beforeScrollStart');
	},

	_move: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault ) {	// increases performance on Android? TODO: check!
			e.preventDefault();
		}

		var point		= e.touches ? e.touches[0] : e,
			deltaX		= point.pageX - this.pointX,
			deltaY		= point.pageY - this.pointY,
			timestamp	= utils.getTime(),
			newX, newY,
			absDistX, absDistY;

		this.pointX		= point.pageX;
		this.pointY		= point.pageY;

		this.distX		+= deltaX;
		this.distY		+= deltaY;
		absDistX		= Math.abs(this.distX);
		absDistY		= Math.abs(this.distY);

		// We need to move at least 10 pixels for the scrolling to initiate
		if ( timestamp - this.endTime > 300 && (absDistX < 10 && absDistY < 10) ) {
			return;
		}

		// If you are scrolling in one direction lock the other
		if ( !this.directionLocked && !this.options.freeScroll ) {
			if ( absDistX > absDistY + this.options.directionLockThreshold ) {
				this.directionLocked = 'h';		// lock horizontally
			} else if ( absDistY >= absDistX + this.options.directionLockThreshold ) {
				this.directionLocked = 'v';		// lock vertically
			} else {
				this.directionLocked = 'n';		// no lock
			}
		}

		if ( this.directionLocked == 'h' ) {
			if ( this.options.eventPassthrough == 'vertical' ) {
				e.preventDefault();
			} else if ( this.options.eventPassthrough == 'horizontal' ) {
				this.initiated = false;
				return;
			}

			deltaY = 0;
		} else if ( this.directionLocked == 'v' ) {
			if ( this.options.eventPassthrough == 'horizontal' ) {
				e.preventDefault();
			} else if ( this.options.eventPassthrough == 'vertical' ) {
				this.initiated = false;
				return;
			}

			deltaX = 0;
		}

		deltaX = this.hasHorizontalScroll ? deltaX : 0;
		deltaY = this.hasVerticalScroll ? deltaY : 0;

		newX = this.x + deltaX;
		newY = this.y + deltaY;

		// Slow down if outside of the boundaries
		if ( newX > 0 || newX < this.maxScrollX ) {
			newX = this.options.bounce ? this.x + deltaX / 3 : newX > 0 ? 0 : this.maxScrollX;
		}
		if ( newY > 0 || newY < this.maxScrollY ) {
			newY = this.options.bounce ? this.y + deltaY / 3 : newY > 0 ? 0 : this.maxScrollY;
		}

		this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
		this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

		if ( !this.moved ) {
			this._execEvent('scrollStart');
		}

		this.moved = true;

		this._translate(newX, newY);

/* REPLACE START: _move */

		if ( timestamp - this.startTime > 300 ) {
			this.startTime = timestamp;
			this.startX = this.x;
			this.startY = this.y;
		}

/* REPLACE END: _move */

	},

	_end: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault && !utils.preventDefaultException(e.target, this.options.preventDefaultException) ) {
			e.preventDefault();
		}

		var point = e.changedTouches ? e.changedTouches[0] : e,
			momentumX,
			momentumY,
			duration = utils.getTime() - this.startTime,
			newX = Math.round(this.x),
			newY = Math.round(this.y),
			distanceX = Math.abs(newX - this.startX),
			distanceY = Math.abs(newY - this.startY),
			time = 0,
			easing = '';

		this.isInTransition = 0;
		this.initiated = 0;
		this.endTime = utils.getTime();

		// reset if we are outside of the boundaries
		if ( this.resetPosition(this.options.bounceTime) ) {
			return;
		}

		this.scrollTo(newX, newY);	// ensures that the last position is rounded

		// we scrolled less than 10 pixels
		if ( !this.moved ) {
			if ( this.options.tap ) {
				utils.tap(e, this.options.tap);
			}

			if ( this.options.click ) {
				utils.click(e);
			}

			this._execEvent('scrollCancel');
			return;
		}

		if ( this._events.flick && duration < 200 && distanceX < 100 && distanceY < 100 ) {
			this._execEvent('flick');
			return;
		}

		// start momentum animation if needed
		if ( this.options.momentum && duration < 300 ) {
			momentumX = this.hasHorizontalScroll ? utils.momentum(this.x, this.startX, duration, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration) : { destination: newX, duration: 0 };
			momentumY = this.hasVerticalScroll ? utils.momentum(this.y, this.startY, duration, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration) : { destination: newY, duration: 0 };
			newX = momentumX.destination;
			newY = momentumY.destination;
			time = Math.max(momentumX.duration, momentumY.duration);
			this.isInTransition = 1;
		}


		if ( this.options.snap ) {
			var snap = this._nearestSnap(newX, newY);
			this.currentPage = snap;
			time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(newX - snap.x), 1000),
						Math.min(Math.abs(newY - snap.y), 1000)
					), 300);
			newX = snap.x;
			newY = snap.y;

			this.directionX = 0;
			this.directionY = 0;
			easing = this.options.bounceEasing;
		}

// INSERT POINT: _end

		if ( newX != this.x || newY != this.y ) {
			// change easing function when scroller goes out of the boundaries
			if ( newX > 0 || newX < this.maxScrollX || newY > 0 || newY < this.maxScrollY ) {
				easing = utils.ease.quadratic;
			}

			this.scrollTo(newX, newY, time, easing);
			return;
		}

		this._execEvent('scrollEnd');
	},

	_resize: function () {
		var that = this;

		clearTimeout(this.resizeTimeout);

		this.resizeTimeout = setTimeout(function () {
			that.refresh();
		}, this.options.resizePolling);
	},

	resetPosition: function (time) {
		var x = this.x,
			y = this.y;

		time = time || 0;

		if ( !this.hasHorizontalScroll || this.x > 0 ) {
			x = 0;
		} else if ( this.x < this.maxScrollX ) {
			x = this.maxScrollX;
		}

		if ( !this.hasVerticalScroll || this.y > 0 ) {
			y = 0;
		} else if ( this.y < this.maxScrollY ) {
			y = this.maxScrollY;
		}

		if ( x == this.x && y == this.y ) {
			return false;
		}

		this.scrollTo(x, y, time, this.options.bounceEasing);

		return true;
	},

	disable: function () {
		this.enabled = false;
	},

	enable: function () {
		this.enabled = true;
	},

	refresh: function () {
		var rf = this.wrapper.offsetHeight;		// Force reflow

		this.wrapperWidth	= this.wrapper.clientWidth;
		this.wrapperHeight	= this.wrapper.clientHeight;

/* REPLACE START: refresh */
	this.scrollerWidth	= Math.round(this.scroller.offsetWidth * this.scale);
	this.scrollerHeight	= Math.round(this.scroller.offsetHeight * this.scale);

	this.maxScrollX		= this.wrapperWidth - this.scrollerWidth;
	this.maxScrollY		= this.wrapperHeight - this.scrollerHeight;
/* REPLACE END: refresh */

		this.hasHorizontalScroll	= this.options.scrollX && this.maxScrollX < 0;
		this.hasVerticalScroll		= this.options.scrollY && this.maxScrollY < 0;

		if ( !this.hasHorizontalScroll ) {
			this.maxScrollX = 0;
			this.scrollerWidth = this.wrapperWidth;
		}

		if ( !this.hasVerticalScroll ) {
			this.maxScrollY = 0;
			this.scrollerHeight = this.wrapperHeight;
		}

		this.endTime = 0;
		this.directionX = 0;
		this.directionY = 0;

		this.wrapperOffset = utils.offset(this.wrapper);

		this._execEvent('refresh');

		this.resetPosition(this.options.bounceTime);

// INSERT POINT: _refresh

	},

	on: function (type, fn) {
		if ( !this._events[type] ) {
			this._events[type] = [];
		}

		this._events[type].push(fn);
	},

	off: function (type, fn) {
		if ( !this._events[type] ) {
			return;
		}

		var index = this._events[type].indexOf(fn);

		if ( index > -1 ) {
			this._events[type].splice(index, 1);
		}
	},

	_execEvent: function (type) {
		if ( !this._events[type] ) {
			return;
		}

		var i = 0,
			l = this._events[type].length;

		if ( !l ) {
			return;
		}

		for ( ; i < l; i++ ) {
			this._events[type][i].apply(this, [].slice.call(arguments, 1));
		}
	},

	scrollBy: function (x, y, time, easing) {
		x = this.x + x;
		y = this.y + y;
		time = time || 0;

		this.scrollTo(x, y, time, easing);
	},

	scrollTo: function (x, y, time, easing) {
		easing = easing || utils.ease.circular;

		this.isInTransition = this.options.useTransition && time > 0;

		if ( !time || (this.options.useTransition && easing.style) ) {
			this._transitionTimingFunction(easing.style);
			this._transitionTime(time);
			this._translate(x, y);
		} else {
			this._animate(x, y, time, easing.fn);
		}
	},

	scrollToElement: function (el, time, offsetX, offsetY, easing) {
		el = el.nodeType ? el : this.scroller.querySelector(el);

		if ( !el ) {
			return;
		}

		var pos = utils.offset(el);

		pos.left -= this.wrapperOffset.left;
		pos.top  -= this.wrapperOffset.top;

		// if offsetX/Y are true we center the element to the screen
		if ( offsetX === true ) {
			offsetX = Math.round(el.offsetWidth / 2 - this.wrapper.offsetWidth / 2);
		}
		if ( offsetY === true ) {
			offsetY = Math.round(el.offsetHeight / 2 - this.wrapper.offsetHeight / 2);
		}

		pos.left -= offsetX || 0;
		pos.top  -= offsetY || 0;

		pos.left = pos.left > 0 ? 0 : pos.left < this.maxScrollX ? this.maxScrollX : pos.left;
		pos.top  = pos.top  > 0 ? 0 : pos.top  < this.maxScrollY ? this.maxScrollY : pos.top;

		time = time === undefined || time === null || time === 'auto' ? Math.max(Math.abs(this.x-pos.left), Math.abs(this.y-pos.top)) : time;

		this.scrollTo(pos.left, pos.top, time, easing);
	},

	_transitionTime: function (time) {
		time = time || 0;

		this.scrollerStyle[utils.style.transitionDuration] = time + 'ms';

		if ( !time && utils.isBadAndroid ) {
			this.scrollerStyle[utils.style.transitionDuration] = '0.001s';
		}


		if ( this.indicators ) {
			for ( var i = this.indicators.length; i--; ) {
				this.indicators[i].transitionTime(time);
			}
		}


// INSERT POINT: _transitionTime

	},

	_transitionTimingFunction: function (easing) {
		this.scrollerStyle[utils.style.transitionTimingFunction] = easing;


		if ( this.indicators ) {
			for ( var i = this.indicators.length; i--; ) {
				this.indicators[i].transitionTimingFunction(easing);
			}
		}


// INSERT POINT: _transitionTimingFunction

	},

	_translate: function (x, y) {
		if ( this.options.useTransform ) {

/* REPLACE START: _translate */			this.scrollerStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px) scale(' + this.scale + ') ' + this.translateZ;/* REPLACE END: _translate */

		} else {
			x = Math.round(x);
			y = Math.round(y);
			this.scrollerStyle.left = x + 'px';
			this.scrollerStyle.top = y + 'px';
		}

		this.x = x;
		this.y = y;


	if ( this.indicators ) {
		for ( var i = this.indicators.length; i--; ) {
			this.indicators[i].updatePosition();
		}
	}


// INSERT POINT: _translate

	},

	_initEvents: function (remove) {
		var eventType = remove ? utils.removeEvent : utils.addEvent,
			target = this.options.bindToWrapper ? this.wrapper : window;

		eventType(window, 'orientationchange', this);
		eventType(window, 'resize', this);

		if ( this.options.click ) {
			eventType(this.wrapper, 'click', this, true);
		}

		if ( !this.options.disableMouse ) {
			eventType(this.wrapper, 'mousedown', this);
			eventType(target, 'mousemove', this);
			eventType(target, 'mousecancel', this);
			eventType(target, 'mouseup', this);
		}

		if ( utils.hasPointer && !this.options.disablePointer ) {
			eventType(this.wrapper, utils.prefixPointerEvent('pointerdown'), this);
			eventType(target, utils.prefixPointerEvent('pointermove'), this);
			eventType(target, utils.prefixPointerEvent('pointercancel'), this);
			eventType(target, utils.prefixPointerEvent('pointerup'), this);
		}

		if ( utils.hasTouch && !this.options.disableTouch ) {
			eventType(this.wrapper, 'touchstart', this);
			eventType(target, 'touchmove', this);
			eventType(target, 'touchcancel', this);
			eventType(target, 'touchend', this);
		}

		eventType(this.scroller, 'transitionend', this);
		eventType(this.scroller, 'webkitTransitionEnd', this);
		eventType(this.scroller, 'oTransitionEnd', this);
		eventType(this.scroller, 'MSTransitionEnd', this);
	},

	getComputedPosition: function () {
		var matrix = window.getComputedStyle(this.scroller, null),
			x, y;

		if ( this.options.useTransform ) {
			matrix = matrix[utils.style.transform].split(')')[0].split(', ');
			x = +(matrix[12] || matrix[4]);
			y = +(matrix[13] || matrix[5]);
		} else {
			x = +matrix.left.replace(/[^-\d.]/g, '');
			y = +matrix.top.replace(/[^-\d.]/g, '');
		}

		return { x: x, y: y };
	},

	_initIndicators: function () {
		var interactive = this.options.interactiveScrollbars,
			customStyle = typeof this.options.scrollbars != 'string',
			indicators = [],
			indicator;

		var that = this;

		this.indicators = [];

		if ( this.options.scrollbars ) {
			// Vertical scrollbar
			if ( this.options.scrollY ) {
				indicator = {
					el: createDefaultScrollbar('v', interactive, this.options.scrollbars),
					interactive: interactive,
					defaultScrollbars: true,
					customStyle: customStyle,
					resize: this.options.resizeScrollbars,
					shrink: this.options.shrinkScrollbars,
					fade: this.options.fadeScrollbars,
					listenX: false
				};

				this.wrapper.appendChild(indicator.el);
				indicators.push(indicator);
			}

			// Horizontal scrollbar
			if ( this.options.scrollX ) {
				indicator = {
					el: createDefaultScrollbar('h', interactive, this.options.scrollbars),
					interactive: interactive,
					defaultScrollbars: true,
					customStyle: customStyle,
					resize: this.options.resizeScrollbars,
					shrink: this.options.shrinkScrollbars,
					fade: this.options.fadeScrollbars,
					listenY: false
				};

				this.wrapper.appendChild(indicator.el);
				indicators.push(indicator);
			}
		}

		if ( this.options.indicators ) {
			// TODO: check concat compatibility
			indicators = indicators.concat(this.options.indicators);
		}

		for ( var i = indicators.length; i--; ) {
			this.indicators.push( new Indicator(this, indicators[i]) );
		}

		// TODO: check if we can use array.map (wide compatibility and performance issues)
		function _indicatorsMap (fn) {
			for ( var i = that.indicators.length; i--; ) {
				fn.call(that.indicators[i]);
			}
		}

		if ( this.options.fadeScrollbars ) {
			this.on('scrollEnd', function () {
				_indicatorsMap(function () {
					this.fade();
				});
			});

			this.on('scrollCancel', function () {
				_indicatorsMap(function () {
					this.fade();
				});
			});

			this.on('scrollStart', function () {
				_indicatorsMap(function () {
					this.fade(1);
				});
			});

			this.on('beforeScrollStart', function () {
				_indicatorsMap(function () {
					this.fade(1, true);
				});
			});
		}


		this.on('refresh', function () {
			_indicatorsMap(function () {
				this.refresh();
			});
		});

		this.on('destroy', function () {
			_indicatorsMap(function () {
				this.destroy();
			});

			delete this.indicators;
		});
	},

	_initZoom: function () {
		this.scrollerStyle[utils.style.transformOrigin] = '0 0';
	},

	_zoomStart: function (e) {
		var c1 = Math.abs( e.touches[0].pageX - e.touches[1].pageX ),
			c2 = Math.abs( e.touches[0].pageY - e.touches[1].pageY );

		this.touchesDistanceStart = Math.sqrt(c1 * c1 + c2 * c2);
		this.startScale = this.scale;

		this.originX = Math.abs(e.touches[0].pageX + e.touches[1].pageX) / 2 + this.wrapperOffset.left - this.x;
		this.originY = Math.abs(e.touches[0].pageY + e.touches[1].pageY) / 2 + this.wrapperOffset.top - this.y;

		this._execEvent('zoomStart');
	},

	_zoom: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault ) {
			e.preventDefault();
		}

		var c1 = Math.abs( e.touches[0].pageX - e.touches[1].pageX ),
			c2 = Math.abs( e.touches[0].pageY - e.touches[1].pageY ),
			distance = Math.sqrt( c1 * c1 + c2 * c2 ),
			scale = 1 / this.touchesDistanceStart * distance * this.startScale,
			lastScale,
			x, y;

		this.scaled = true;

		if ( scale < this.options.zoomMin ) {
			scale = 0.5 * this.options.zoomMin * Math.pow(2.0, scale / this.options.zoomMin);
		} else if ( scale > this.options.zoomMax ) {
			scale = 2.0 * this.options.zoomMax * Math.pow(0.5, this.options.zoomMax / scale);
		}

		lastScale = scale / this.startScale;
		x = this.originX - this.originX * lastScale + this.startX;
		y = this.originY - this.originY * lastScale + this.startY;

		this.scale = scale;

		this.scrollTo(x, y, 0);
	},

	_zoomEnd: function (e) {
		if ( !this.enabled || utils.eventType[e.type] !== this.initiated ) {
			return;
		}

		if ( this.options.preventDefault ) {
			e.preventDefault();
		}

		var newX, newY,
			lastScale;

		this.isInTransition = 0;
		this.initiated = 0;

		if ( this.scale > this.options.zoomMax ) {
			this.scale = this.options.zoomMax;
		} else if ( this.scale < this.options.zoomMin ) {
			this.scale = this.options.zoomMin;
		}

		// Update boundaries
		this.refresh();

		lastScale = this.scale / this.startScale;

		newX = this.originX - this.originX * lastScale + this.startX;
		newY = this.originY - this.originY * lastScale + this.startY;

		if ( newX > 0 ) {
			newX = 0;
		} else if ( newX < this.maxScrollX ) {
			newX = this.maxScrollX;
		}

		if ( newY > 0 ) {
			newY = 0;
		} else if ( newY < this.maxScrollY ) {
			newY = this.maxScrollY;
		}

		this.scrollTo(newX, newY, this.options.bounceTime);

		this.scaled = false;

		this._execEvent('zoomEnd');
	},

	zoom: function (scale, x, y, time) {
		if ( scale < this.options.zoomMin ) {
			scale = this.options.zoomMin;
		} else if ( scale > this.options.zoomMax ) {
			scale = this.options.zoomMax;
		}

		if ( scale == this.scale ) {
			return;
		}

		var relScale = scale / this.scale;

		x = x === undefined ? this.wrapperWidth / 2 : x;
		y = y === undefined ? this.wrapperHeight / 2 : y;
		time = time === undefined ? 300 : time;

		x = x + this.wrapperOffset.left - this.x;
		y = y + this.wrapperOffset.top - this.y;

		x = x - x * relScale + this.x;
		y = y - y * relScale + this.y;

		this.scale = scale;

		this.refresh();		// update boundaries

		if ( x > 0 ) {
			x = 0;
		} else if ( x < this.maxScrollX ) {
			x = this.maxScrollX;
		}

		if ( y > 0 ) {
			y = 0;
		} else if ( y < this.maxScrollY ) {
			y = this.maxScrollY;
		}

		this.scrollTo(x, y, time);
	},

	_wheelZoom: function (e) {
		var wheelDeltaY,
			deltaScale,
			that = this;

		// Execute the zoomEnd event after 400ms the wheel stopped scrolling
		clearTimeout(this.wheelTimeout);
		this.wheelTimeout = setTimeout(function () {
			that._execEvent('zoomEnd');
		}, 400);

		if ( 'deltaX' in e ) {
			wheelDeltaY = -e.deltaY / Math.abs(e.deltaY);
		} else if ('wheelDeltaX' in e) {
			wheelDeltaY = e.wheelDeltaY / Math.abs(e.wheelDeltaY);
		} else if('wheelDelta' in e) {
			wheelDeltaY = e.wheelDelta / Math.abs(e.wheelDelta);
		} else if ('detail' in e) {
			wheelDeltaY = -e.detail / Math.abs(e.detail);
		} else {
			return;
		}

		if (isNaN(wheelDeltaY)) wheelDeltaY = 0;
		deltaScale = this.scale + wheelDeltaY * .01;

		this.zoom(deltaScale, e.pageX, e.pageY, 0);

		e.preventDefault();
		e.stopPropagation();
	},

	_initWheel: function () {
		utils.addEvent(this.wrapper, 'wheel', this);
		utils.addEvent(this.wrapper, 'mousewheel', this);
		utils.addEvent(this.wrapper, 'DOMMouseScroll', this);

		this.on('destroy', function () {
			utils.removeEvent(this.wrapper, 'wheel', this);
			utils.removeEvent(this.wrapper, 'mousewheel', this);
			utils.removeEvent(this.wrapper, 'DOMMouseScroll', this);
		});
	},

	_wheel: function (e) {
		if ( !this.enabled ) {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		var wheelDeltaX, wheelDeltaY,
			newX, newY,
			that = this;

		if ( this.wheelTimeout === undefined ) {
			that._execEvent('scrollStart');
		}

		// Execute the scrollEnd event after 400ms the wheel stopped scrolling
		clearTimeout(this.wheelTimeout);
		this.wheelTimeout = setTimeout(function () {
			that._execEvent('scrollEnd');
			that.wheelTimeout = undefined;
		}, 400);

		if ( 'deltaX' in e ) {
			if (e.deltaMode === 1) {
				wheelDeltaX = -e.deltaX * this.options.mouseWheelSpeed;
				wheelDeltaY = -e.deltaY * this.options.mouseWheelSpeed;
			} else {
				wheelDeltaX = -e.deltaX;
				wheelDeltaY = -e.deltaY;
			}
		} else if ( 'wheelDeltaX' in e ) {
			wheelDeltaX = e.wheelDeltaX / 120 * this.options.mouseWheelSpeed;
			wheelDeltaY = e.wheelDeltaY / 120 * this.options.mouseWheelSpeed;
		} else if ( 'wheelDelta' in e ) {
			wheelDeltaX = wheelDeltaY = e.wheelDelta / 120 * this.options.mouseWheelSpeed;
		} else if ( 'detail' in e ) {
			wheelDeltaX = wheelDeltaY = -e.detail / 3 * this.options.mouseWheelSpeed;
		} else {
			return;
		}

		wheelDeltaX *= this.options.invertWheelDirection;
		wheelDeltaY *= this.options.invertWheelDirection;

		if ( !this.hasVerticalScroll ) {
			wheelDeltaX = wheelDeltaY;
			wheelDeltaY = 0;
		}

		if ( this.options.snap ) {
			newX = this.currentPage.pageX;
			newY = this.currentPage.pageY;

			if ( wheelDeltaX > 0 ) {
				newX--;
			} else if ( wheelDeltaX < 0 ) {
				newX++;
			}

			if ( wheelDeltaY > 0 ) {
				newY--;
			} else if ( wheelDeltaY < 0 ) {
				newY++;
			}

			this.goToPage(newX, newY);

			return;
		}

		newX = this.x + Math.round(this.hasHorizontalScroll ? wheelDeltaX : 0);
		newY = this.y + Math.round(this.hasVerticalScroll ? wheelDeltaY : 0);

		if ( newX > 0 ) {
			newX = 0;
		} else if ( newX < this.maxScrollX ) {
			newX = this.maxScrollX;
		}

		if ( newY > 0 ) {
			newY = 0;
		} else if ( newY < this.maxScrollY ) {
			newY = this.maxScrollY;
		}

		this.scrollTo(newX, newY, 0);

// INSERT POINT: _wheel
	},

	_initSnap: function () {
		this.currentPage = {};

		if ( typeof this.options.snap == 'string' ) {
			this.options.snap = this.scroller.querySelectorAll(this.options.snap);
		}

		this.on('refresh', function () {
			var i = 0, l,
				m = 0, n,
				cx, cy,
				x = 0, y,
				stepX = this.options.snapStepX || this.wrapperWidth,
				stepY = this.options.snapStepY || this.wrapperHeight,
				el;

			this.pages = [];

			if ( !this.wrapperWidth || !this.wrapperHeight || !this.scrollerWidth || !this.scrollerHeight ) {
				return;
			}

			if ( this.options.snap === true ) {
				cx = Math.round( stepX / 2 );
				cy = Math.round( stepY / 2 );

				while ( x > -this.scrollerWidth ) {
					this.pages[i] = [];
					l = 0;
					y = 0;

					while ( y > -this.scrollerHeight ) {
						this.pages[i][l] = {
							x: Math.max(x, this.maxScrollX),
							y: Math.max(y, this.maxScrollY),
							width: stepX,
							height: stepY,
							cx: x - cx,
							cy: y - cy
						};

						y -= stepY;
						l++;
					}

					x -= stepX;
					i++;
				}
			} else {
				el = this.options.snap;
				l = el.length;
				n = -1;

				for ( ; i < l; i++ ) {
					if ( i === 0 || el[i].offsetLeft <= el[i-1].offsetLeft ) {
						m = 0;
						n++;
					}

					if ( !this.pages[m] ) {
						this.pages[m] = [];
					}

					x = Math.max(-el[i].offsetLeft, this.maxScrollX);
					y = Math.max(-el[i].offsetTop, this.maxScrollY);
					cx = x - Math.round(el[i].offsetWidth / 2);
					cy = y - Math.round(el[i].offsetHeight / 2);

					this.pages[m][n] = {
						x: x,
						y: y,
						width: el[i].offsetWidth,
						height: el[i].offsetHeight,
						cx: cx,
						cy: cy
					};

					if ( x > this.maxScrollX ) {
						m++;
					}
				}
			}

			this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0);

			// Update snap threshold if needed
			if ( this.options.snapThreshold % 1 === 0 ) {
				this.snapThresholdX = this.options.snapThreshold;
				this.snapThresholdY = this.options.snapThreshold;
			} else {
				this.snapThresholdX = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold);
				this.snapThresholdY = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold);
			}
		});

		this.on('flick', function () {
			var time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(this.x - this.startX), 1000),
						Math.min(Math.abs(this.y - this.startY), 1000)
					), 300);

			this.goToPage(
				this.currentPage.pageX + this.directionX,
				this.currentPage.pageY + this.directionY,
				time
			);
		});
	},

	_nearestSnap: function (x, y) {
		if ( !this.pages.length ) {
			return { x: 0, y: 0, pageX: 0, pageY: 0 };
		}

		var i = 0,
			l = this.pages.length,
			m = 0;

		// Check if we exceeded the snap threshold
		if ( Math.abs(x - this.absStartX) < this.snapThresholdX &&
			Math.abs(y - this.absStartY) < this.snapThresholdY ) {
			return this.currentPage;
		}

		if ( x > 0 ) {
			x = 0;
		} else if ( x < this.maxScrollX ) {
			x = this.maxScrollX;
		}

		if ( y > 0 ) {
			y = 0;
		} else if ( y < this.maxScrollY ) {
			y = this.maxScrollY;
		}

		for ( ; i < l; i++ ) {
			if ( x >= this.pages[i][0].cx ) {
				x = this.pages[i][0].x;
				break;
			}
		}

		l = this.pages[i].length;

		for ( ; m < l; m++ ) {
			if ( y >= this.pages[0][m].cy ) {
				y = this.pages[0][m].y;
				break;
			}
		}

		if ( i == this.currentPage.pageX ) {
			i += this.directionX;

			if ( i < 0 ) {
				i = 0;
			} else if ( i >= this.pages.length ) {
				i = this.pages.length - 1;
			}

			x = this.pages[i][0].x;
		}

		if ( m == this.currentPage.pageY ) {
			m += this.directionY;

			if ( m < 0 ) {
				m = 0;
			} else if ( m >= this.pages[0].length ) {
				m = this.pages[0].length - 1;
			}

			y = this.pages[0][m].y;
		}

		return {
			x: x,
			y: y,
			pageX: i,
			pageY: m
		};
	},

	goToPage: function (x, y, time, easing) {
		easing = easing || this.options.bounceEasing;

		if ( x >= this.pages.length ) {
			x = this.pages.length - 1;
		} else if ( x < 0 ) {
			x = 0;
		}

		if ( y >= this.pages[x].length ) {
			y = this.pages[x].length - 1;
		} else if ( y < 0 ) {
			y = 0;
		}

		var posX = this.pages[x][y].x,
			posY = this.pages[x][y].y;

		time = time === undefined ? this.options.snapSpeed || Math.max(
			Math.max(
				Math.min(Math.abs(posX - this.x), 1000),
				Math.min(Math.abs(posY - this.y), 1000)
			), 300) : time;

		this.currentPage = {
			x: posX,
			y: posY,
			pageX: x,
			pageY: y
		};

		this.scrollTo(posX, posY, time, easing);
	},

	next: function (time, easing) {
		var x = this.currentPage.pageX,
			y = this.currentPage.pageY;

		x++;

		if ( x >= this.pages.length && this.hasVerticalScroll ) {
			x = 0;
			y++;
		}

		this.goToPage(x, y, time, easing);
	},

	prev: function (time, easing) {
		var x = this.currentPage.pageX,
			y = this.currentPage.pageY;

		x--;

		if ( x < 0 && this.hasVerticalScroll ) {
			x = 0;
			y--;
		}

		this.goToPage(x, y, time, easing);
	},

	_initKeys: function (e) {
		// default key bindings
		var keys = {
			pageUp: 33,
			pageDown: 34,
			end: 35,
			home: 36,
			left: 37,
			up: 38,
			right: 39,
			down: 40
		};
		var i;

		// if you give me characters I give you keycode
		if ( typeof this.options.keyBindings == 'object' ) {
			for ( i in this.options.keyBindings ) {
				if ( typeof this.options.keyBindings[i] == 'string' ) {
					this.options.keyBindings[i] = this.options.keyBindings[i].toUpperCase().charCodeAt(0);
				}
			}
		} else {
			this.options.keyBindings = {};
		}

		for ( i in keys ) {
			this.options.keyBindings[i] = this.options.keyBindings[i] || keys[i];
		}

		utils.addEvent(window, 'keydown', this);

		this.on('destroy', function () {
			utils.removeEvent(window, 'keydown', this);
		});
	},

	_key: function (e) {
		if ( !this.enabled ) {
			return;
		}

		var snap = this.options.snap,	// we are using this alot, better to cache it
			newX = snap ? this.currentPage.pageX : this.x,
			newY = snap ? this.currentPage.pageY : this.y,
			now = utils.getTime(),
			prevTime = this.keyTime || 0,
			acceleration = 0.250,
			pos;

		if ( this.options.useTransition && this.isInTransition ) {
			pos = this.getComputedPosition();

			this._translate(Math.round(pos.x), Math.round(pos.y));
			this.isInTransition = false;
		}

		this.keyAcceleration = now - prevTime < 200 ? Math.min(this.keyAcceleration + acceleration, 50) : 0;

		switch ( e.keyCode ) {
			case this.options.keyBindings.pageUp:
				if ( this.hasHorizontalScroll && !this.hasVerticalScroll ) {
					newX += snap ? 1 : this.wrapperWidth;
				} else {
					newY += snap ? 1 : this.wrapperHeight;
				}
				break;
			case this.options.keyBindings.pageDown:
				if ( this.hasHorizontalScroll && !this.hasVerticalScroll ) {
					newX -= snap ? 1 : this.wrapperWidth;
				} else {
					newY -= snap ? 1 : this.wrapperHeight;
				}
				break;
			case this.options.keyBindings.end:
				newX = snap ? this.pages.length-1 : this.maxScrollX;
				newY = snap ? this.pages[0].length-1 : this.maxScrollY;
				break;
			case this.options.keyBindings.home:
				newX = 0;
				newY = 0;
				break;
			case this.options.keyBindings.left:
				newX += snap ? -1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.up:
				newY += snap ? 1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.right:
				newX -= snap ? -1 : 5 + this.keyAcceleration>>0;
				break;
			case this.options.keyBindings.down:
				newY -= snap ? 1 : 5 + this.keyAcceleration>>0;
				break;
			default:
				return;
		}

		if ( snap ) {
			this.goToPage(newX, newY);
			return;
		}

		if ( newX > 0 ) {
			newX = 0;
			this.keyAcceleration = 0;
		} else if ( newX < this.maxScrollX ) {
			newX = this.maxScrollX;
			this.keyAcceleration = 0;
		}

		if ( newY > 0 ) {
			newY = 0;
			this.keyAcceleration = 0;
		} else if ( newY < this.maxScrollY ) {
			newY = this.maxScrollY;
			this.keyAcceleration = 0;
		}

		this.scrollTo(newX, newY, 0);

		this.keyTime = now;
	},

	_animate: function (destX, destY, duration, easingFn) {
		var that = this,
			startX = this.x,
			startY = this.y,
			startTime = utils.getTime(),
			destTime = startTime + duration;

		function step () {
			var now = utils.getTime(),
				newX, newY,
				easing;

			if ( now >= destTime ) {
				that.isAnimating = false;
				that._translate(destX, destY);

				if ( !that.resetPosition(that.options.bounceTime) ) {
					that._execEvent('scrollEnd');
				}

				return;
			}

			now = ( now - startTime ) / duration;
			easing = easingFn(now);
			newX = ( destX - startX ) * easing + startX;
			newY = ( destY - startY ) * easing + startY;
			that._translate(newX, newY);

			if ( that.isAnimating ) {
				rAF(step);
			}
		}

		this.isAnimating = true;
		step();
	},
	handleEvent: function (e) {
		switch ( e.type ) {
			case 'touchstart':
			case 'pointerdown':
			case 'MSPointerDown':
			case 'mousedown':
				this._start(e);

				if ( this.options.zoom && e.touches && e.touches.length > 1 ) {
					this._zoomStart(e);
				}
				break;
			case 'touchmove':
			case 'pointermove':
			case 'MSPointerMove':
			case 'mousemove':
				if ( this.options.zoom && e.touches && e.touches[1] ) {
					this._zoom(e);
					return;
				}
				this._move(e);
				break;
			case 'touchend':
			case 'pointerup':
			case 'MSPointerUp':
			case 'mouseup':
			case 'touchcancel':
			case 'pointercancel':
			case 'MSPointerCancel':
			case 'mousecancel':
				if ( this.scaled ) {
					this._zoomEnd(e);
					return;
				}
				this._end(e);
				break;
			case 'orientationchange':
			case 'resize':
				this._resize();
				break;
			case 'transitionend':
			case 'webkitTransitionEnd':
			case 'oTransitionEnd':
			case 'MSTransitionEnd':
				this._transitionEnd(e);
				break;
			case 'wheel':
			case 'DOMMouseScroll':
			case 'mousewheel':
				if ( this.options.wheelAction == 'zoom' ) {
					this._wheelZoom(e);
					return;
				}
				this._wheel(e);
				break;
			case 'keydown':
				this._key(e);
				break;
		}
	}

};
function createDefaultScrollbar (direction, interactive, type) {
	var scrollbar = document.createElement('div'),
		indicator = document.createElement('div');

	if ( type === true ) {
		scrollbar.style.cssText = 'position:absolute;z-index:9999';
		indicator.style.cssText = '-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px';
	}

	indicator.className = 'iScrollIndicator';

	if ( direction == 'h' ) {
		if ( type === true ) {
			scrollbar.style.cssText += ';height:7px;left:2px;right:2px;bottom:0';
			indicator.style.height = '100%';
		}
		scrollbar.className = 'iScrollHorizontalScrollbar';
	} else {
		if ( type === true ) {
			scrollbar.style.cssText += ';width:7px;bottom:2px;top:2px;right:1px';
			indicator.style.width = '100%';
		}
		scrollbar.className = 'iScrollVerticalScrollbar';
	}

	scrollbar.style.cssText += ';overflow:hidden';

	if ( !interactive ) {
		scrollbar.style.pointerEvents = 'none';
	}

	scrollbar.appendChild(indicator);

	return scrollbar;
}

function Indicator (scroller, options) {
	this.wrapper = typeof options.el == 'string' ? document.querySelector(options.el) : options.el;
	this.wrapperStyle = this.wrapper.style;
	this.indicator = this.wrapper.children[0];
	this.indicatorStyle = this.indicator.style;
	this.scroller = scroller;

	this.options = {
		listenX: true,
		listenY: true,
		interactive: false,
		resize: true,
		defaultScrollbars: false,
		shrink: false,
		fade: false,
		speedRatioX: 0,
		speedRatioY: 0
	};

	for ( var i in options ) {
		this.options[i] = options[i];
	}

	this.sizeRatioX = 1;
	this.sizeRatioY = 1;
	this.maxPosX = 0;
	this.maxPosY = 0;

	if ( this.options.interactive ) {
		if ( !this.options.disableTouch ) {
			utils.addEvent(this.indicator, 'touchstart', this);
			utils.addEvent(window, 'touchend', this);
		}
		if ( !this.options.disablePointer ) {
			utils.addEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
			utils.addEvent(window, utils.prefixPointerEvent('pointerup'), this);
		}
		if ( !this.options.disableMouse ) {
			utils.addEvent(this.indicator, 'mousedown', this);
			utils.addEvent(window, 'mouseup', this);
		}
	}

	if ( this.options.fade ) {
		this.wrapperStyle[utils.style.transform] = this.scroller.translateZ;
		this.wrapperStyle[utils.style.transitionDuration] = utils.isBadAndroid ? '0.001s' : '0ms';
		this.wrapperStyle.opacity = '0';
	}
}

Indicator.prototype = {
	handleEvent: function (e) {
		switch ( e.type ) {
			case 'touchstart':
			case 'pointerdown':
			case 'MSPointerDown':
			case 'mousedown':
				this._start(e);
				break;
			case 'touchmove':
			case 'pointermove':
			case 'MSPointerMove':
			case 'mousemove':
				this._move(e);
				break;
			case 'touchend':
			case 'pointerup':
			case 'MSPointerUp':
			case 'mouseup':
			case 'touchcancel':
			case 'pointercancel':
			case 'MSPointerCancel':
			case 'mousecancel':
				this._end(e);
				break;
		}
	},

	destroy: function () {
		if ( this.options.interactive ) {
			utils.removeEvent(this.indicator, 'touchstart', this);
			utils.removeEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
			utils.removeEvent(this.indicator, 'mousedown', this);

			utils.removeEvent(window, 'touchmove', this);
			utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
			utils.removeEvent(window, 'mousemove', this);

			utils.removeEvent(window, 'touchend', this);
			utils.removeEvent(window, utils.prefixPointerEvent('pointerup'), this);
			utils.removeEvent(window, 'mouseup', this);
		}

		if ( this.options.defaultScrollbars ) {
			this.wrapper.parentNode.removeChild(this.wrapper);
		}
	},

	_start: function (e) {
		var point = e.touches ? e.touches[0] : e;

		e.preventDefault();
		e.stopPropagation();

		this.transitionTime();

		this.initiated = true;
		this.moved = false;
		this.lastPointX	= point.pageX;
		this.lastPointY	= point.pageY;

		this.startTime	= utils.getTime();

		if ( !this.options.disableTouch ) {
			utils.addEvent(window, 'touchmove', this);
		}
		if ( !this.options.disablePointer ) {
			utils.addEvent(window, utils.prefixPointerEvent('pointermove'), this);
		}
		if ( !this.options.disableMouse ) {
			utils.addEvent(window, 'mousemove', this);
		}

		this.scroller._execEvent('beforeScrollStart');
	},

	_move: function (e) {
		var point = e.touches ? e.touches[0] : e,
			deltaX, deltaY,
			newX, newY,
			timestamp = utils.getTime();

		if ( !this.moved ) {
			this.scroller._execEvent('scrollStart');
		}

		this.moved = true;

		deltaX = point.pageX - this.lastPointX;
		this.lastPointX = point.pageX;

		deltaY = point.pageY - this.lastPointY;
		this.lastPointY = point.pageY;

		newX = this.x + deltaX;
		newY = this.y + deltaY;

		this._pos(newX, newY);

// INSERT POINT: indicator._move

		e.preventDefault();
		e.stopPropagation();
	},

	_end: function (e) {
		if ( !this.initiated ) {
			return;
		}

		this.initiated = false;

		e.preventDefault();
		e.stopPropagation();

		utils.removeEvent(window, 'touchmove', this);
		utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
		utils.removeEvent(window, 'mousemove', this);

		if ( this.scroller.options.snap ) {
			var snap = this.scroller._nearestSnap(this.scroller.x, this.scroller.y);

			var time = this.options.snapSpeed || Math.max(
					Math.max(
						Math.min(Math.abs(this.scroller.x - snap.x), 1000),
						Math.min(Math.abs(this.scroller.y - snap.y), 1000)
					), 300);

			if ( this.scroller.x != snap.x || this.scroller.y != snap.y ) {
				this.scroller.directionX = 0;
				this.scroller.directionY = 0;
				this.scroller.currentPage = snap;
				this.scroller.scrollTo(snap.x, snap.y, time, this.scroller.options.bounceEasing);
			}
		}

		if ( this.moved ) {
			this.scroller._execEvent('scrollEnd');
		}
	},

	transitionTime: function (time) {
		time = time || 0;
		this.indicatorStyle[utils.style.transitionDuration] = time + 'ms';

		if ( !time && utils.isBadAndroid ) {
			this.indicatorStyle[utils.style.transitionDuration] = '0.001s';
		}
	},

	transitionTimingFunction: function (easing) {
		this.indicatorStyle[utils.style.transitionTimingFunction] = easing;
	},

	refresh: function () {
		this.transitionTime();

		if ( this.options.listenX && !this.options.listenY ) {
			this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? 'block' : 'none';
		} else if ( this.options.listenY && !this.options.listenX ) {
			this.indicatorStyle.display = this.scroller.hasVerticalScroll ? 'block' : 'none';
		} else {
			this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? 'block' : 'none';
		}

		if ( this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll ) {
			utils.addClass(this.wrapper, 'iScrollBothScrollbars');
			utils.removeClass(this.wrapper, 'iScrollLoneScrollbar');

			if ( this.options.defaultScrollbars && this.options.customStyle ) {
				if ( this.options.listenX ) {
					this.wrapper.style.right = '8px';
				} else {
					this.wrapper.style.bottom = '8px';
				}
			}
		} else {
			utils.removeClass(this.wrapper, 'iScrollBothScrollbars');
			utils.addClass(this.wrapper, 'iScrollLoneScrollbar');

			if ( this.options.defaultScrollbars && this.options.customStyle ) {
				if ( this.options.listenX ) {
					this.wrapper.style.right = '2px';
				} else {
					this.wrapper.style.bottom = '2px';
				}
			}
		}

		var r = this.wrapper.offsetHeight;	// force refresh

		if ( this.options.listenX ) {
			this.wrapperWidth = this.wrapper.clientWidth;
			if ( this.options.resize ) {
				this.indicatorWidth = Math.max(Math.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8);
				this.indicatorStyle.width = this.indicatorWidth + 'px';
			} else {
				this.indicatorWidth = this.indicator.clientWidth;
			}

			this.maxPosX = this.wrapperWidth - this.indicatorWidth;

			if ( this.options.shrink == 'clip' ) {
				this.minBoundaryX = -this.indicatorWidth + 8;
				this.maxBoundaryX = this.wrapperWidth - 8;
			} else {
				this.minBoundaryX = 0;
				this.maxBoundaryX = this.maxPosX;
			}

			this.sizeRatioX = this.options.speedRatioX || (this.scroller.maxScrollX && (this.maxPosX / this.scroller.maxScrollX));
		}

		if ( this.options.listenY ) {
			this.wrapperHeight = this.wrapper.clientHeight;
			if ( this.options.resize ) {
				this.indicatorHeight = Math.max(Math.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8);
				this.indicatorStyle.height = this.indicatorHeight + 'px';
			} else {
				this.indicatorHeight = this.indicator.clientHeight;
			}

			this.maxPosY = this.wrapperHeight - this.indicatorHeight;

			if ( this.options.shrink == 'clip' ) {
				this.minBoundaryY = -this.indicatorHeight + 8;
				this.maxBoundaryY = this.wrapperHeight - 8;
			} else {
				this.minBoundaryY = 0;
				this.maxBoundaryY = this.maxPosY;
			}

			this.maxPosY = this.wrapperHeight - this.indicatorHeight;
			this.sizeRatioY = this.options.speedRatioY || (this.scroller.maxScrollY && (this.maxPosY / this.scroller.maxScrollY));
		}

		this.updatePosition();
	},

	updatePosition: function () {
		var x = this.options.listenX && Math.round(this.sizeRatioX * this.scroller.x) || 0,
			y = this.options.listenY && Math.round(this.sizeRatioY * this.scroller.y) || 0;

		if ( !this.options.ignoreBoundaries ) {
			if ( x < this.minBoundaryX ) {
				if ( this.options.shrink == 'scale' ) {
					this.width = Math.max(this.indicatorWidth + x, 8);
					this.indicatorStyle.width = this.width + 'px';
				}
				x = this.minBoundaryX;
			} else if ( x > this.maxBoundaryX ) {
				if ( this.options.shrink == 'scale' ) {
					this.width = Math.max(this.indicatorWidth - (x - this.maxPosX), 8);
					this.indicatorStyle.width = this.width + 'px';
					x = this.maxPosX + this.indicatorWidth - this.width;
				} else {
					x = this.maxBoundaryX;
				}
			} else if ( this.options.shrink == 'scale' && this.width != this.indicatorWidth ) {
				this.width = this.indicatorWidth;
				this.indicatorStyle.width = this.width + 'px';
			}

			if ( y < this.minBoundaryY ) {
				if ( this.options.shrink == 'scale' ) {
					this.height = Math.max(this.indicatorHeight + y * 3, 8);
					this.indicatorStyle.height = this.height + 'px';
				}
				y = this.minBoundaryY;
			} else if ( y > this.maxBoundaryY ) {
				if ( this.options.shrink == 'scale' ) {
					this.height = Math.max(this.indicatorHeight - (y - this.maxPosY) * 3, 8);
					this.indicatorStyle.height = this.height + 'px';
					y = this.maxPosY + this.indicatorHeight - this.height;
				} else {
					y = this.maxBoundaryY;
				}
			} else if ( this.options.shrink == 'scale' && this.height != this.indicatorHeight ) {
				this.height = this.indicatorHeight;
				this.indicatorStyle.height = this.height + 'px';
			}
		}

		this.x = x;
		this.y = y;

		if ( this.scroller.options.useTransform ) {
			this.indicatorStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.scroller.translateZ;
		} else {
			this.indicatorStyle.left = x + 'px';
			this.indicatorStyle.top = y + 'px';
		}
	},

	_pos: function (x, y) {
		if ( x < 0 ) {
			x = 0;
		} else if ( x > this.maxPosX ) {
			x = this.maxPosX;
		}

		if ( y < 0 ) {
			y = 0;
		} else if ( y > this.maxPosY ) {
			y = this.maxPosY;
		}

		x = this.options.listenX ? Math.round(x / this.sizeRatioX) : this.scroller.x;
		y = this.options.listenY ? Math.round(y / this.sizeRatioY) : this.scroller.y;

		this.scroller.scrollTo(x, y);
	},

	fade: function (val, hold) {
		if ( hold && !this.visible ) {
			return;
		}

		clearTimeout(this.fadeTimeout);
		this.fadeTimeout = null;

		var time = val ? 250 : 500,
			delay = val ? 0 : 300;

		val = val ? '1' : '0';

		this.wrapperStyle[utils.style.transitionDuration] = time + 'ms';

		this.fadeTimeout = setTimeout((function (val) {
			this.wrapperStyle.opacity = val;
			this.visible = +val;
		}).bind(this, val), delay);
	}
};

IScroll.utils = utils;

if ( typeof module != 'undefined' && module.exports ) {
	module.exports = IScroll;
} else {
	window.IScroll = IScroll;
}

})(window, document, Math);
(function($) {
'use strict';

$.fn.photoClip = function(option) {
	if (!window.FileReader) {
		alert("您的浏览器不支持 HTML5 的 FileReader API， 因此无法初始化图片裁剪插件，请更换最新的浏览器！");
		return;
	}

	var defaultOption = {
		width: 200,
		height: 200,
		file: "",
		view: "",
		ok: "",
		strictSize: false,
		loadStart: function() {},
		loadComplete: function() {},
		loadError: function() {},
		clipFinish: function() {}
	}
	$.extend(defaultOption, option);

	this.each(function() {
		photoClip(this, defaultOption);
	});

	return this;
}

function photoClip(container, option) {
	var clipWidth = option.width,
		clipHeight = option.height,
		file = option.file,
		view = option.view,
		ok = option.ok,
		strictSize = option.strictSize,
		loadStart = option.loadStart,
		loadComplete = option.loadComplete,
		loadError = option.loadError,
		clipFinish = option.clipFinish;

	var $file = $(file);
	if (!$file.length) return;

	var $img,
		imgWidth, imgHeight, //图片当前的宽高
		imgLoaded; //图片是否已经加载完成

	$file.attr("accept", "image/*");
	$file.change(function() {
		if (!this.files.length) return;
		if (!/image\/\w+/.test(this.files[0].type)) {
			alert("图片格式不正确，请选择正确格式的图片文件！");
			return false;
		} else {
			var fileReader = new FileReader();
			fileReader.onprogress = function(e) {
				console.log((e.loaded / e.total * 100).toFixed() + "%");
			};
			fileReader.onload = function(e) {
				var kbs = e.total / 1024;
				if (kbs > 1024) {
					// 图片大于1M，需要压缩
					var quality = 1024 / kbs;
					var $tempImg = $("<img>").hide();
					$tempImg.load(function() {
						// IOS 设备中，如果的照片是竖屏拍摄的，虽然实际在网页中显示出的方向也是垂直，但图片数据依然是以横屏方向展示
						var sourceWidth = this.naturalWidth; // 在没有加入文档前，jQuery无法获得正确宽高，但可以通过原生属性来读取
						$tempImg.appendTo(document.body);
						var realityHeight = this.naturalHeight;
						$tempImg.remove();
						delete $tempImg[0];
						$tempImg = null;
						var angleOffset = 0;
						if (sourceWidth == realityHeight) {
							angleOffset = 90;
						}
						// 将图片进行压缩
						var newDataURL = compressImg(this, quality, angleOffset);
						createImg(newDataURL);
					});
					$tempImg.attr("src", this.result);
				} else {
					createImg(this.result);
				}
			};
			fileReader.onerror = function(e) {
				alert("图片加载失败");
				loadError.call(this, e);
			};
			fileReader.readAsDataURL(this.files[0]); // 读取文件内容
			//保存文件名
			$('#hit').attr('fileName',this.files[0].name);
			loadStart.call(fileReader, this.files[0]);
		}
	});

	$file.click(function() {
		this.value = "";
	});



	var $container, // 容器，包含裁剪视图层和遮罩层
		$clipView, // 裁剪视图层，包含移动层
		$moveLayer, // 移动层，包含旋转层
		$rotateLayer, // 旋转层
		$view, // 最终截图后呈现的视图容器
		canvas, // 图片裁剪用到的画布
		myScroll, // 图片的scroll对象，包含图片的位置与缩放信息
		containerWidth,
		containerHeight;

	init();
	initScroll();
	initEvent();
	initClip();

	var $ok = $(ok);
	if ($ok.length) {
		$ok.click(function() {
			$('.lazy_cover,.lazy_tip').hide();
			clipImg();
		});
	}

	var $win = $(window);
	resize();
	$win.resize(resize);

	var atRotation, // 是否正在旋转中
		curX, // 旋转层的当前X坐标
		curY, // 旋转层的当前Y坐标
		curAngle; // 旋转层的当前角度

	function imgLoad() {
		imgLoaded = true;

		$rotateLayer.append(this);

		hideAction.call(this, $img, function() {
			imgWidth = this.naturalWidth;
			imgHeight = this.naturalHeight;
		});

		hideAction($moveLayer, function() {
			resetScroll();
		});


		loadComplete.call(this, this.src);
	}

	function initScroll() {
		var options = {
			zoom: true,
			scrollX: true,
			scrollY: true,
			freeScroll: true,
			mouseWheel: true,
			wheelAction: "zoom"
		}
		myScroll = new IScroll($clipView[0], options);
	}
	function resetScroll() {
		curX = 0;
		curY = 0;
		curAngle = 0;

		$rotateLayer.css({
			"width": imgWidth,
			"height": imgHeight
		});
		setTransform($rotateLayer, curX, curY, curAngle);

		calculateScale(imgWidth, imgHeight);
		myScroll.zoom(myScroll.options.zoomStart);
		refreshScroll(imgWidth, imgHeight);

		var posX = (clipWidth - imgWidth * myScroll.options.zoomStart) * .5,
			posY = (clipHeight - imgHeight * myScroll.options.zoomStart) * .5;
		myScroll.scrollTo(posX, posY);
	}
	function refreshScroll(width, height) {
		$moveLayer.css({
			"width": width,
			"height": height
		});
		// 在移动设备上，尤其是Android设备，当为一个元素重置了宽高时
		// 该元素的offsetWidth/offsetHeight、clientWidth/clientHeight等属性并不会立即更新，导致相关的js程序出现错误
		// iscroll 在刷新方法中正是使用了 offsetWidth/offsetHeight 来获取scroller元素($moveLayer)的宽高
		// 因此需要手动将元素重新添加进文档，迫使浏览器强制更新元素的宽高
		$clipView.append($moveLayer);
		myScroll.refresh();
	}

	function initEvent() {
		var is_mobile = !!navigator.userAgent.match(/mobile/i);

		if (is_mobile) {
			var hammerManager = new Hammer($moveLayer[0]);
			hammerManager.add(new Hammer.Rotate());

			var rotation, rotateDirection;
			hammerManager.on("rotatemove", function(e) {
				if (atRotation) return;
				rotation = e.rotation;
				if (rotation > 180) {
					rotation -= 360;
				} else if (rotation < -180) {
					rotation += 360  ;
				}
				rotateDirection = rotation > 0 ? 1 : rotation < 0 ? -1 : 0;
			});
			hammerManager.on("rotateend", function(e) {
				if (atRotation) return;

				if (Math.abs(rotation) > 30) {
					if (rotateDirection == 1) {
						// 顺时针
						rotateCW(e.center);
					} else if (rotateDirection == -1) {
						// 逆时针
						rotateCCW(e.center);
					}
				}
			});
		} else {
			$moveLayer.on("dblclick", function(e) {
				rotateCW({
					x: e.clientX,
					y: e.clientY
				});
			});
		}
	}
	function rotateCW(point) {
		rotateBy(90, point);
	}
	function rotateCCW(point) {
		rotateBy(-90, point);
	}
	function rotateBy(angle, point) {
		if (atRotation) return;
		atRotation = true;

		var loacl;
		if (!point) {
			loacl = loaclToLoacl($moveLayer, $clipView, clipWidth * .5, clipHeight * .5);
		} else {
			loacl = globalToLoacl($moveLayer, point.x, point.y);
		}
		var origin = calculateOrigin(curAngle, loacl), // 旋转中使用的参考点坐标
			originX = origin.x,
			originY = origin.y,

			// 旋转层以零位为参考点旋转到新角度后的位置，与以当前计算的参考点“从零度”旋转到新角度后的位置，之间的左上角偏移量
			offsetX = 0, offsetY = 0,
			// 移动层当前的位置（即旋转层旋转前的位置），与旋转层以当前计算的参考点从当前角度旋转到新角度后的位置，之间的左上角偏移量
			parentOffsetX = 0, parentOffsetY = 0,

			newAngle = curAngle + angle,

			curImgWidth, // 移动层的当前宽度
			curImgHeight; // 移动层的当前高度


		if (newAngle == 90 || newAngle == -270)
		{
			offsetX = originX + originY;
			offsetY = originY - originX;

			if (newAngle > curAngle) {
				parentOffsetX = imgHeight - originX - originY;
				parentOffsetY = originX - originY;
			} else if (newAngle < curAngle) {
				parentOffsetX = (imgHeight - originY) - (imgWidth - originX);
				parentOffsetY = originX + originY - imgHeight;
			}

			curImgWidth = imgHeight;
			curImgHeight = imgWidth;
		}
		else if (newAngle == 180 || newAngle == -180)
		{
			offsetX = originX * 2;
			offsetY = originY * 2;

			if (newAngle > curAngle) {
				parentOffsetX = (imgWidth - originX) - (imgHeight - originY);
				parentOffsetY = imgHeight - (originX + originY);
			} else if (newAngle < curAngle) {
				parentOffsetX = imgWidth - (originX + originY);
				parentOffsetY = (imgHeight - originY) - (imgWidth - originX);
			}

			curImgWidth = imgWidth;
			curImgHeight = imgHeight;
		}
		else if (newAngle == 270 || newAngle == -90)
		{
			offsetX = originX - originY;
			offsetY = originX + originY;

			if (newAngle > curAngle) {
				parentOffsetX = originX + originY - imgWidth;
				parentOffsetY = (imgWidth - originX) - (imgHeight - originY);
			} else if (newAngle < curAngle) {
				parentOffsetX = originY - originX;
				parentOffsetY = imgWidth - originX - originY;
			}

			curImgWidth = imgHeight;
			curImgHeight = imgWidth;
		}
		else if (newAngle == 0 || newAngle == 360 || newAngle == -360)
		{
			offsetX = 0;
			offsetY = 0;

			if (newAngle > curAngle) {
				parentOffsetX = originX - originY;
				parentOffsetY = originX + originY - imgWidth;
			} else if (newAngle < curAngle) {
				parentOffsetX = originX + originY - imgHeight;
				parentOffsetY = originY - originX;
			}

			curImgWidth = imgWidth;
			curImgHeight = imgHeight;
		}

		// 将触摸点设为旋转时的参考点
		// 改变参考点的同时，要计算坐标的偏移，从而保证图片位置不发生变化
		if (curAngle == 0) {
			curX = 0;
			curY = 0;
		} else if (curAngle == 90 || curAngle == -270) {
			curX -= originX + originY;
			curY -= originY - originX;
		} else if (curAngle == 180 || curAngle == -180) {
			curX -= originX * 2;
			curY -= originY * 2;
		} else if (curAngle == 270 || curAngle == -90) {
			curX -= originX - originY;
			curY -= originX + originY;
		}
		curX = curX.toFixed(2) - 0;
		curY = curY.toFixed(2) - 0;
		setTransform($rotateLayer, curX, curY, curAngle, originX, originY);

		// 开始旋转
		setTransition($rotateLayer, curX, curY, newAngle, 200, function() {
			atRotation = false;
			curAngle = newAngle % 360;
			// 旋转完成后将参考点设回零位
			// 同时加上偏移，保证图片位置看上去没有变化
			// 这里要另外要加上父容器（移动层）零位与自身之间的偏移量
			curX += offsetX + parentOffsetX;
			curY += offsetY + parentOffsetY;
			curX = curX.toFixed(2) - 0;
			curY = curY.toFixed(2) - 0;
			setTransform($rotateLayer, curX, curY, curAngle);
			// 相应的父容器（移动层）要减去与旋转层之间的偏移量
			// 这样看上去就好像图片没有移动
			myScroll.scrollTo(
				myScroll.x - parentOffsetX * myScroll.scale,
				myScroll.y - parentOffsetY * myScroll.scale
			);
			calculateScale(curImgWidth, curImgHeight);
			if (myScroll.scale < myScroll.options.zoomMin) {
				myScroll.zoom(myScroll.options.zoomMin);
			}

			refreshScroll(curImgWidth, curImgHeight);
		});
	}

	function initClip() {
		canvas = document.createElement("canvas");
		canvas.width = clipWidth;
		canvas.height = clipHeight;
	}
	function clipImg() {
		if (!imgLoaded) {
			alert("亲，当前没有图片可以裁剪!");
			$('.lazy_cover,.lazy_tip').hide();
			return;
		}
		var local = loaclToLoacl($moveLayer, $clipView);
		var scale = myScroll.scale;
		var ctx = canvas.getContext("2d");
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		ctx.save();

		if (strictSize) {
			ctx.scale(scale, scale);
		} else {
			canvas.width = clipWidth / scale;
			canvas.height = clipHeight / scale;
		}

		ctx.translate(curX - local.x / scale, curY - local.y / scale);
		ctx.rotate(curAngle * Math.PI / 180);

		ctx.drawImage($img[0], 0, 0);
		ctx.restore();

		var dataURL = canvas.toDataURL("image/jpeg");
		$view.css("background-image", "url("+ dataURL +")");
		clipFinish.call($img[0], dataURL);
		$('.lazy_cover,.lazy_tip').hide();
	}


	function resize() {
		hideAction($container, function() {
			containerWidth = $container.width();
			containerHeight = $container.height();
		});
	}
	function loaclToLoacl($layerOne, $layerTwo, x, y) { // 计算$layerTwo上的x、y坐标在$layerOne上的坐标
		x = x || 0;
		y = y || 0;
		var layerOneOffset, layerTwoOffset;
		hideAction($layerOne, function() {
			layerOneOffset = $layerOne.offset();
		});
		hideAction($layerTwo, function() {
			layerTwoOffset = $layerTwo.offset();
		});
		return {
			x: layerTwoOffset.left - layerOneOffset.left + x,
			y: layerTwoOffset.top - layerOneOffset.top + y
		};
	}
	function globalToLoacl($layer, x, y) { // 计算相对于窗口的x、y坐标在$layer上的坐标
		x = x || 0;
		y = y || 0;
		var layerOffset;
		hideAction($layer, function() {
			layerOffset = $layer.offset();
		});
		return {
			x: x + $win.scrollLeft() - layerOffset.left,
			y: y + $win.scrollTop() - layerOffset.top
		};
	}
	function hideAction(jq, func) {
		var $hide = $();
		$.each(jq, function(i, n){
			var $n = $(n);
			var $hidden = $n.parents().andSelf().filter(":hidden");
			var $none;
			for (var i = 0; i < $hidden.length; i++) {
				if (!$n.is(":hidden")) break;
				$none = $hidden.eq(i);
				if ($none.css("display") == "none") $hide = $hide.add($none.show());
			}
		});
		if (typeof(func) == "function") func.call(this);
		$hide.hide();
	}
	function calculateOrigin(curAngle, point) {
		var scale = myScroll.scale;
		var origin = {};
		if (curAngle == 0) {
			origin.x = point.x / scale;
			origin.y = point.y / scale;
		} else if (curAngle == 90 || curAngle == -270) {
			origin.x = point.y / scale;
			origin.y = imgHeight - point.x / scale;
		} else if (curAngle == 180 || curAngle == -180) {
			origin.x = imgWidth - point.x / scale;
			origin.y = imgHeight - point.y / scale;
		} else if (curAngle == 270 || curAngle == -90) {
			origin.x = imgWidth - point.y / scale;
			origin.y = point.x / scale;
		}
		return origin;
	}
	function getScale(w1, h1, w2, h2) {
		var sx = w1 / w2;
		var sy = h1 / h2;
		return sx > sy ? sx : sy;
	}
	function calculateScale(width, height) {
		myScroll.options.zoomMin = getScale(clipWidth, clipHeight, width, height);
		myScroll.options.zoomMax = Math.max(1, myScroll.options.zoomMin);
		myScroll.options.zoomStart = Math.min(myScroll.options.zoomMax, getScale(containerWidth, containerHeight, width, height));
	}
	function compressImg(sourceImgObj, quality, angleOffset, outputFormat){
		quality = quality || .8;
		angleOffset = angleOffset || 0;
		var mimeType = "image/jpeg";
		if (outputFormat != undefined && outputFormat == "png") {
			mimeType = "image/png";
		}

		var drawWidth = sourceImgObj.naturalWidth,
			drawHeight = sourceImgObj.naturalHeight;
		// IOS 设备上 canvas 宽或高如果大于 1024，就有可能导致应用崩溃闪退
		// 因此这里需要缩放
		var maxSide = Math.max(drawWidth, drawHeight);
		if (maxSide > 1024) {
			var minSide = Math.min(drawWidth, drawHeight);
			minSide = minSide / maxSide * 1024;
			maxSide = 1024;
			if (drawWidth > drawHeight) {
				drawWidth = maxSide;
				drawHeight = minSide;
			} else {
				drawWidth = minSide;
				drawHeight = maxSide;
			}
		}

		var cvs = document.createElement('canvas');
		var ctx = cvs.getContext("2d");
		if (angleOffset) {
			cvs.width = drawHeight;
			cvs.height = drawWidth;
			ctx.translate(drawHeight, 0);
			ctx.rotate(angleOffset * Math.PI / 180);
		} else {
			cvs.width = drawWidth;
			cvs.height = drawHeight;
		}

		ctx.drawImage(sourceImgObj, 0, 0, drawWidth, drawHeight);
		var newImageData = cvs.toDataURL(mimeType, quality || .8);
		return newImageData;
	}
	function createImg(src) {
		if ($img && $img.length) {
			// 删除旧的图片以释放内存，防止IOS设备的webview崩溃
			$img.remove();
			delete $img[0];
		}
		$img = $("<img>").css({
			"user-select": "none",
			"pointer-events": "none"
		});
		$img.load(imgLoad);
		$img.attr("src", src); // 设置图片base64值
	}

	function setTransform($obj, x, y, angle, originX, originY) {
		originX = originX || 0;
		originY = originY || 0;
		var style = {};
		style[prefix + "transform"] = "translateZ(0) translate(" + x + "px," + y + "px) rotate(" + angle + "deg)";
		style[prefix + "transform-origin"] = originX + "px " + originY + "px";
		$obj.css(style);
	}
	function setTransition($obj, x, y, angle, dur, fn) {
		// 这里需要先读取之前设置好的transform样式，强制浏览器将该样式值渲染到元素
		// 否则浏览器可能出于性能考虑，将暂缓样式渲染，等到之后所有样式设置完成后再统一渲染
		// 这样就会导致之前设置的位移也被应用到动画中
		$obj.css(prefix + "transform");
		$obj.css(prefix + "transition", prefix + "transform " + dur + "ms");
		$obj.one(transitionEnd, function() {
			$obj.css(prefix + "transition", "");
			fn.call(this);
		});
		$obj.css(prefix + "transform", "translateZ(0) translate(" + x + "px," + y + "px) rotate(" + angle + "deg)");
	}

	function init() {
		// 初始化容器
		$container = $(container).css({
			"user-select": "none",
			"overflow": "hidden"
		});
		if ($container.css("position") == "static") $container.css("position", "relative");

		// 创建裁剪视图层
		$clipView = $("<div class='photo-clip-view'>").css({
			"position": "absolute",
			"left": "50%",
			"top": "50%",
			"width": clipWidth,
			"height": clipHeight,
			"margin-left": -clipWidth/2,
			"margin-top": -clipHeight/2
		}).appendTo($container);

		$moveLayer = $("<div class='photo-clip-moveLayer'>").appendTo($clipView);

		$rotateLayer = $("<div class='photo-clip-rotateLayer'>").appendTo($moveLayer);

		// 创建遮罩
		var $mask = $("<div class='photo-clip-mask'>").css({
			"position": "absolute",
			"left": 0,
			"top": 0,
			"width": "100%",
			"height": "100%",
			"pointer-events": "none"
		}).appendTo($container);
		var $mask_left = $("<div class='photo-clip-mask-left'>").css({
			"position": "absolute",
			"left": 0,
			"right": "50%",
			"top": "50%",
			"bottom": "50%",
			"width": "auto",
			"height": clipHeight,
			"margin-right": clipWidth/2,
			"margin-top": -clipHeight/2,
			"margin-bottom": -clipHeight/2,
			"background-color": "rgba(0,0,0,.5)"
		}).appendTo($mask);
		var $mask_right = $("<div class='photo-clip-mask-right'>").css({
			"position": "absolute",
			"left": "50%",
			"right": 0,
			"top": "50%",
			"bottom": "50%",
			"margin-left": clipWidth/2,
			"margin-top": -clipHeight/2,
			"margin-bottom": -clipHeight/2,
			"background-color": "rgba(0,0,0,.5)"
		}).appendTo($mask);
		var $mask_top = $("<div class='photo-clip-mask-top'>").css({
			"position": "absolute",
			"left": 0,
			"right": 0,
			"top": 0,
			"bottom": "50%",
			"margin-bottom": clipHeight/2,
			"background-color": "rgba(0,0,0,.5)"
		}).appendTo($mask);
		var $mask_bottom = $("<div class='photo-clip-mask-bottom'>").css({
			"position": "absolute",
			"left": 0,
			"right": 0,
			"top": "50%",
			"bottom": 0,
			"margin-top": clipHeight/2,
			"background-color": "rgba(0,0,0,.5)"
		}).appendTo($mask);
		// 创建截取区域
		var $clip_area = $("<div class='photo-clip-area'>").css({
			"border": "1px dashed #ddd",
			"position": "absolute",
			"left": "50%",
			"top": "50%",
			"width": clipWidth,
			"height": clipHeight,
			"margin-left": -clipWidth/2 - 1,
			"margin-top": -clipHeight/2 - 1
		}).appendTo($mask);

		// 初始化视图容器
		$view = $(view);
		if ($view.length) {
			$view.css({
				"background-color": "#666",
				"background-repeat": "no-repeat",
				"background-position": "center",
				"background-size": "contain"
			});
		}
	}
}

var prefix = '',
	transitionEnd;

(function() {

	var eventPrefix,
		vendors = { Webkit: 'webkit', Moz: '', O: 'o' },
    	testEl = document.documentElement,
    	normalizeEvent = function(name) { return eventPrefix ? eventPrefix + name : name.toLowerCase() };

	for (var i in vendors) {
		if (testEl.style[i + 'TransitionProperty'] !== undefined) {
			prefix = '-' + i.toLowerCase() + '-';
			eventPrefix = vendors[i];
			break;
		}
	}

	transitionEnd = normalizeEvent('TransitionEnd');

})();

})(jQuery);
