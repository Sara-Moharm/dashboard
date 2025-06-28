import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext
from transformers import AutoTokenizer, AutoModelForSequenceClassification
import torch
import numpy as np
import re
from collections import Counter
 

class SentimentApp:
    def __init__(self, root):
        self.root = root
        root.title("Restaurant Review Analyzer")
        root.geometry("1000x700")
        root.configure(bg="#f0f0f0")

        # Aspect keywords dictionary
        self.aspect_keywords = {
            "🍴 Food Quality": [
                "food",
                "dish",
                "meal",
                "crepe",
                "pizza",
                "cuisine",
                "menu",
                "taste",
                "flavor",
                "delicious",
                "yummy",
                "tasty",
                "spicy",
                "bland",
                "overcooked",
                "undercooked",
                "fresh",
                "frozen",
                "portion",
                "presentation",
            ],
            "💁 Service": [
                "service",
                "waiter",
                "serve",
                "waitress",
                "staff",
                "server",
                "manager",
                "host",
                "attention",
                "friendly",
                "rude",
                "attentive",
                "ignored",
                "order",
                "recommendation",
            ],
            "🎭 Ambience": [
                "ambience",
                "atmosphere",
                "place",
                "sea",
                "vibe",
                "belonging",
                "decor",
                "lighting",
                "music",
                "noise",
                "loud",
                "quiet",
                "romantic",
                "crowded",
                "spacious",
                "seating",
                "view",
                "relaxing",
                "nice",
                "outdoor",
            ],
            "💰 Price/Value": [
                "price",
                "cost",
                "expensive",
                "cheap",
                "affordable",
                "value",
                "worth",
                "overpriced",
                "budget",
                "bill",
                "discount",
                "deal",
                "pricey",
                "reasonable",
            ],
            "🧹 Cleanliness": [
                "clean",
                "dirty",
                "hygiene",
                "table",
                "restroom",
                "toilet",
                "utensils",
                "floor",
                "napkin",
                "sticky",
                "smell",
                "odor",
                "spotless",
                "messy",
            ],
            "⏳ Waiting Time": [
                "wait",
                "time",
                "quick",
                "deliver",
                "late",
                "early",
                "slow",
                "busy",
                "crowded",
                "reservation",
                "seated",
                "prompt",
                "delay",
                "immediate",
                "fast",
                "minutes",
            ],
            "🍷 Drinks": [
                "drink",
                "drinks",
                "wine",
                "cocktail",
                "beer",
                "coffee",
                "tea",
                "water",
                "refill",
                "bar",
                "bartender",
                "mocktail",
                "sommelier",
                "pairing",
            ],
            "🍰 Desserts": [
                "dessert",
                "cake",
                "sweet",
                "ice cream",
                "pancakes",
                "chocolate",
                "pastry",
                "pie",
                "tiramisu",
                "baklava",
                "creme brulee",
            ],
        }

        # Load model
        self.model_loaded = False
        self.load_model()

        # GUI Elements
        self.create_widgets()

    def load_model(self):
        """Load model with error handling"""
        try:
            self.status_var = tk.StringVar(value="Loading model...")
            self.root.update()

            self.device = "cuda" if torch.cuda.is_available() else "cpu"
            self.tokenizer = AutoTokenizer.from_pretrained("./sentiment_model")
            self.model = AutoModelForSequenceClassification.from_pretrained(
                "./sentiment_model"
            ).to(self.device)
            self.labels = ["Negative", "Neutral", "Positive"]

            self.model_loaded = True
            self.status_var.set("Model ready")
        except Exception as e:
            messagebox.showerror("Error", f"Failed to load model:\n{str(e)}")
            self.status_var.set("Model failed to load")

    def create_widgets(self):
        """Create all GUI components"""
        # Main container
        main_frame = ttk.Frame(self.root, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Header
        header = ttk.Label(
            main_frame,
            text="Restaurant Review Sentiment & Aspect Analyzer",
            font=("Helvetica", 16, "bold"),
            foreground="#2c3e50",
        )
        header.pack(pady=10)

        # Input area
        input_frame = ttk.LabelFrame(main_frame, text="Enter Review", padding=10)
        input_frame.pack(fill=tk.X, pady=10)

        self.text_input = scrolledtext.ScrolledText(
            input_frame, height=10, wrap=tk.WORD, font=("Arial", 11), padx=10, pady=10
        )
        self.text_input.pack(fill=tk.BOTH, expand=True)

        # Aspect selection
        aspect_frame = ttk.Frame(main_frame)
        aspect_frame.pack(fill=tk.X, pady=5)

        ttk.Label(aspect_frame, text="Analyze Aspect:").pack(side=tk.LEFT, padx=5)
        self.aspect_var = tk.StringVar()
        self.aspect_dropdown = ttk.Combobox(
            aspect_frame,
            textvariable=self.aspect_var,
            values=["Overall"] + list(self.aspect_keywords.keys()),
            state="readonly",
            width=25,
        )
        self.aspect_dropdown.current(0)
        self.aspect_dropdown.pack(side=tk.LEFT)

        # Button
        btn_frame = ttk.Frame(main_frame)
        btn_frame.pack(pady=10)

        style = ttk.Style()
        style.configure("Accent.TButton", foreground="blue", background="#3498db")

        self.analyze_btn = ttk.Button(
            btn_frame, text="Analyze", command=self.analyze_text, style="Accent.TButton"
        )
        self.analyze_btn.pack(pady=5, ipadx=10, ipady=5)

        # Results
        results_frame = ttk.LabelFrame(main_frame, text="Analysis Results", padding=10)
        results_frame.pack(fill=tk.BOTH, expand=True, pady=10)

        # Result tabs
        self.notebook = ttk.Notebook(results_frame)
        self.notebook.pack(fill=tk.BOTH, expand=True)

        # Sentiment tab
        self.sentiment_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.sentiment_tab, text="Sentiment")

        self.result_label = ttk.Label(
            self.sentiment_tab,
            text="Overall sentiment will appear here",
            font=("Helvetica", 12),
            foreground="#2c3e50",
            wraplength=700,
        )
        self.result_label.pack(fill=tk.X, pady=5)

        self.details_frame = ttk.Frame(self.sentiment_tab)
        self.details_frame.pack(fill=tk.X, pady=10)

        # Aspect tab
        self.aspect_tab = ttk.Frame(self.notebook)
        self.notebook.add(self.aspect_tab, text="Aspect Breakdown")

        self.aspect_text = scrolledtext.ScrolledText(
            self.aspect_tab, wrap=tk.WORD, font=("Arial", 10), height=10
        )
        self.aspect_text.pack(fill=tk.BOTH, expand=True)
        self.aspect_text.insert(tk.END, "Aspect-based analysis will appear here")

        # Status bar
        status_bar = ttk.Frame(self.root, relief=tk.SUNKEN)
        status_bar.pack(fill=tk.X, side=tk.BOTTOM)

        ttk.Label(status_bar, textvariable=self.status_var, foreground="#7f8c8d").pack(
            side=tk.LEFT
        )

        ttk.Label(
            status_bar, text=f"Using: {self.device.upper()}", foreground="#7f8c8d"
        ).pack(side=tk.RIGHT)

    # NEW: Gibberish detection
    def is_gibberish(self, text):
        """Detects nonsensical input using linguistic heuristics"""
        # Rule 1: Check ratio of alphabetic characters
        alpha_chars = sum(c.isalpha() for c in text)
        if alpha_chars / len(text) < 0.6 if text else 0:
            return True

        # Rule 2: Check for repeated character patterns
        repeats = sum(1 for c in text if text.count(c) > len(text) / 3)
        if repeats > 3:
            return True

        # Rule 3: Check consonant-to-vowel ratio
        vowels = sum(1 for c in text.lower() if c in "aeiou")
        consonants = sum(1 for c in text.lower() if c.isalpha() and c not in "aeiou")
        if consonants > vowels * 3:  # Normal ratio is ~2:1
            return True

        # Rule 4: Check for dictionary words (basic version)
        common_words = {"the", "and", "was", "were", "this", "that"}
        found_words = sum(1 for word in text.lower().split() if word in common_words)
        if found_words < 2 and len(text.split()) > 3:
            return True

        return False

    def analyze_text(self):
        text = self.text_input.get("1.0", tk.END).strip()
        selected_aspect = self.aspect_var.get()
        # Input validation
        if not text:
            messagebox.showwarning("Warning", "Please enter a review first")
            return
        if self.is_gibberish(text):
            self.result_label.config(
                text="⚠️ Invalid Input: Please enter meaningful text", foreground="red"
            )
            return

        try:
            # Aspect-based analysis if specific aspect selected
            if selected_aspect != "Overall":
                keywords = self.aspect_keywords[selected_aspect]
                aspect_text = self.extract_aspect_text(text, keywords)
                if not aspect_text:
                    messagebox.showinfo(
                        "Info", f"No mentions of {selected_aspect} found"
                    )
                    return
                text = aspect_text

            inputs = self.tokenizer(
                text, return_tensors="pt", truncation=True, max_length=512
            ).to(self.device)

            with torch.no_grad():
                outputs = self.model(**inputs)

            scores = torch.softmax(outputs.logits, dim=1)[0].cpu().numpy()
            dominant_idx = np.argmax(scores)

            # Neutral detection
            neutral_score = scores[1]
            if neutral_score > 0.4:
                self.show_results(
                    label="NEUTRAL (Autodetected)",
                    score=neutral_score,
                    scores=scores,
                    is_neutral=True,
                    aspect=selected_aspect,
                )
            else:
                self.show_results(
                    label=self.labels[dominant_idx],
                    score=scores[dominant_idx],
                    scores=scores,
                    is_neutral=False,
                    aspect=selected_aspect,
                )

            # Update aspect analysis
            self.update_aspect_analysis(text)

        except Exception as e:
            messagebox.showerror("Error", f"Analysis failed:\n{str(e)}")

    def extract_aspect_text(self, text, keywords):
        """Extract sentences mentioning specific aspects"""
        sentences = re.split(r"(?<=[.!?])\s+", text)
        relevant = []
        for sentence in sentences:
            if any(
                re.search(rf"\b{re.escape(keyword)}\b", sentence.lower())
                for keyword in keywords
            ):
                relevant.append(sentence)
        return " ".join(relevant) if relevant else ""

    def show_results(self, label, score, scores, is_neutral, aspect="Overall"):
        """Display results with neutral emphasis"""
        # Determine colors
        if is_neutral:
            main_color = "#f39c12"  # Orange for neutral
            label_text = f"NEUTRAL COMMENT (Score: {score:.1%})"
        else:
            main_color = "#e74c3c" if "Negative" in label else "#2ecc71"
            label_text = f"Predicted: {label} ({score:.1%})"

        # Add aspect info if not overall
        if aspect != "Overall":
            label_text = f"[{aspect}] {label_text}"

        # Update main label
        self.result_label.config(
            text=label_text, foreground=main_color, font=("Helvetica", 14, "bold")
        )

        # Clear previous details
        for widget in self.details_frame.winfo_children():
            widget.destroy()

        # Add visual indicator for neutral detection
        if is_neutral:
            ttk.Label(
                self.details_frame,
                text="⚠️ This comment is predominantly neutral (score >30%)",
                foreground=main_color,
            ).pack(pady=5)

        # Score breakdown
        ttk.Label(self.details_frame, text="Detailed Scores:").pack(anchor=tk.W)

        for i, (sentiment, prob) in enumerate(zip(self.labels, scores)):
            frame = ttk.Frame(self.details_frame)
            frame.pack(fill=tk.X, pady=2)

            ttk.Label(frame, text=sentiment, width=10).pack(side=tk.LEFT)

            # Highlight neutral bar
            bar_color = (
                main_color
                if (is_neutral and sentiment == "Neutral")
                else ("#e74c3c" if sentiment == "Negative" else "#2ecc71")
            )

            style = ttk.Style()
            style.configure(f"Bar{i}.Horizontal.TProgressbar", background=bar_color)

            pb = ttk.Progressbar(
                frame,
                orient=tk.HORIZONTAL,
                length=200,
                mode="determinate",
                value=prob * 100,
                style=f"Bar{i}.Horizontal.TProgressbar",
            )
            pb.pack(side=tk.LEFT, padx=5)

            # Bold neutral percentage if over threshold
            if sentiment == "Neutral" and is_neutral:
                ttk.Label(
                    frame, text=f"{prob:.1%}", font=("TkDefaultFont", 9, "bold")
                ).pack(side=tk.LEFT)
            else:
                ttk.Label(frame, text=f"{prob:.1%}").pack(side=tk.LEFT)


    def update_aspect_analysis(self, text):
        """Generate aspect-based analysis"""
        self.aspect_text.delete(1.0, tk.END)

        analysis = "🔍 Aspect-Based Analysis:\n\n"
        for aspect, keywords in self.aspect_keywords.items():
            aspect_sentences = self.extract_aspect_text(text, keywords)
            if aspect_sentences:
                analysis += f"⭐ {aspect}:\n"
                analysis += f"   - Mentions: {len(aspect_sentences.split('. '))}\n"

                # Get sentiment for this aspect
                inputs = self.tokenizer(
                    aspect_sentences, return_tensors="pt", truncation=True
                ).to(self.device)
                with torch.no_grad():
                    outputs = self.model(**inputs)
                scores = torch.softmax(outputs.logits, dim=1)[0].cpu().numpy()
                dominant_idx = np.argmax(scores)

                analysis += (
                    f"   - Sentiment: {self.labels[dominant_idx]} "
                    f"({scores[dominant_idx]:.1%})\n\n"
                )

        self.aspect_text.insert(tk.END, analysis)


if __name__ == "__main__":
    root = tk.Tk()
    app = SentimentApp(root)
    root.mainloop()
